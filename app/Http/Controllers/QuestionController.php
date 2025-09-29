<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class QuestionController extends Controller
{
    /**
     * 문제 카테고리 선택 페이지
     */
    public function index()
    {
        $questionCategory = Category::where('name', '문제')
            ->with(['subCategories' => function($query) {
                $query->withCount('questions')
                    ->orderBy('slug');
            }])
            ->first();

        $yearData = [];
        if ($questionCategory) {
            foreach ($questionCategory->subCategories as $category) {
                $questionCount = Question::where('sub_category_id', $category->id)->count();

                // 문제가 없는 카테고리는 건너뛰기
                if ($questionCount === 0) {
                    continue;
                }

                // slug 패턴: year-class-no
                $slugParts = explode('-', $category->slug);
                $year = $slugParts[0] ?? null;
                $classTime = $slugParts[1] ?? null;

                if ($year) {
                    if (!isset($yearData[$year])) {
                        $yearData[$year] = [
                            'year' => $year,
                            'total_questions' => 0,
                            'first_category_id' => null,
                            'categories' => []
                        ];
                    }

                    $yearData[$year]['total_questions'] += $questionCount;

                    // 1교시 1번이 첫 번째 카테고리가 되도록 설정
                    if ($classTime == '1' && $slugParts[2] == '1') {
                        $yearData[$year]['first_category_id'] = $category->id;
                    }

                    // 첫 번째 카테고리가 없으면 현재 카테고리를 첫 번째로 설정
                    if (!$yearData[$year]['first_category_id']) {
                        $yearData[$year]['first_category_id'] = $category->id;
                    }

                    $yearData[$year]['categories'][] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'description' => $category->description,
                        'question_count' => $questionCount
                    ];
                }
            }
        }

        // 연도별로 정렬 (최신 연도 먼저)
        krsort($yearData);

        return view('questions.index', compact('yearData'));
    }

    /**
     * 문제 풀기 시작
     */
    public function start($categoryId)
    {
        $category = SubCategory::findOrFail($categoryId);

        // slug에서 년도 추출
        $slugParts = explode('-', $category->slug);
        $year = $slugParts[0] ?? null;

        if (!$year) {
            return redirect()->route('questions.index')
                ->with('error', '유효하지 않은 카테고리입니다.');
        }

        // 같은 년도의 모든 서브카테고리 가져오기
        $yearCategories = SubCategory::where('slug', 'like', $year . '-%')
            ->orderBy('slug')
            ->get();

        // 교시별로 그룹화하여 문제 가져오기
        $questionsByClass = [];
        $allQuestionIds = [];
        $classInfo = [];

        foreach ($yearCategories as $subCategory) {
            $parts = explode('-', $subCategory->slug);
            $classTime = $parts[1] ?? null;

            if (!$classTime) continue;

            $questions = Question::where('sub_category_id', $subCategory->id)
                ->orderBy('no')
                ->get();

            if ($questions->isNotEmpty()) {
                if (!isset($questionsByClass[$classTime])) {
                    $questionsByClass[$classTime] = [];
                    $classInfo[$classTime] = [
                        'name' => $classTime . '교시',
                        'start_index' => count($allQuestionIds),
                        'count' => 0
                    ];
                }

                foreach ($questions as $question) {
                    $questionsByClass[$classTime][] = $question->id;
                    $allQuestionIds[] = $question->id;
                }

                $classInfo[$classTime]['count'] = count($questionsByClass[$classTime]);
            }
        }

        if (empty($allQuestionIds)) {
            return redirect()->route('questions.index')
                ->with('error', '선택한 년도에 문제가 없습니다.');
        }

        // 세션에 문제 정보 저장
        Session::put('quiz_questions', $allQuestionIds);
        Session::put('quiz_current', 0);
        Session::put('quiz_answers', []);
        Session::put('quiz_category', $category->toArray());
        Session::put('quiz_year', $year);
        Session::put('quiz_class_info', $classInfo);
        Session::put('quiz_start_time', now());

        return redirect()->route('questions.solve');
    }

    /**
     * 문제 풀기 페이지
     */
    public function solve()
    {
        $questionIds = Session::get('quiz_questions', []);
        $currentIndex = Session::get('quiz_current', 0);
        $userAnswers = Session::get('quiz_answers', []);
        $category = Session::get('quiz_category');
        $year = Session::get('quiz_year');
        $classInfo = Session::get('quiz_class_info', []);

        if (empty($questionIds) || $currentIndex >= count($questionIds)) {
            return redirect()->route('questions.index');
        }

        $question = Question::find($questionIds[$currentIndex]);
        if (!$question) {
            return redirect()->route('questions.index');
        }

        $totalQuestions = count($questionIds);

        // 현재 문제가 속한 교시 찾기
        $currentClass = null;
        $currentClassInfo = null;
        $classQuestionIds = [];
        foreach ($classInfo as $classTime => $info) {
            if ($currentIndex >= $info['start_index'] &&
                $currentIndex < $info['start_index'] + $info['count']) {
                $currentClass = $classTime;
                $currentClassInfo = $info;
                // 현재 교시의 문제 ID들 추출
                $classQuestionIds = array_slice($questionIds, $info['start_index'], $info['count']);
                break;
            }
        }

        // 현재 교시의 답변된 문제 수 계산 및 정답 여부 확인
        $answeredInClass = 0;
        $classQuestionsCount = count($classQuestionIds);
        $answerResults = [];

        foreach ($classQuestionIds as $qId) {
            if (isset($userAnswers[$qId])) {
                $answeredInClass++;
                // 정답 여부 확인
                $q = Question::find($qId);
                if ($q) {
                    $answerResults[$qId] = ($userAnswers[$qId] == $q->answer);
                }
            }
        }

        // 현재 교시 기준 진행률 계산
        $progress = $classQuestionsCount > 0 ? round(($answeredInClass / $classQuestionsCount) * 100, 0) : 0;

        // 현재 교시 내에서의 인덱스 계산
        $currentClassIndex = $currentClassInfo ? ($currentIndex - $currentClassInfo['start_index']) : 0;

        return view('questions.solve', compact(
            'question',
            'currentIndex',
            'totalQuestions',
            'progress',
            'userAnswers',
            'category',
            'year',
            'classInfo',
            'currentClass',
            'currentClassInfo',
            'classQuestionIds',
            'classQuestionsCount',
            'currentClassIndex',
            'answerResults',
            'answeredInClass'
        ));
    }

    /**
     * 답안 제출 및 다음 문제로 이동
     */
    public function submit(Request $request)
    {
        $request->validate([
            'answer' => 'nullable|integer|min:1|max:5'
        ]);

        $questionIds = Session::get('quiz_questions', []);
        $currentIndex = Session::get('quiz_current', 0);
        $userAnswers = Session::get('quiz_answers', []);

        // 현재 문제의 답안 저장
        if ($currentIndex < count($questionIds) && $request->answer) {
            $userAnswers[$questionIds[$currentIndex]] = $request->answer;
            Session::put('quiz_answers', $userAnswers);
        }

        // save_only 플래그가 있으면 JSON 응답 반환 (AJAX 요청)
        if ($request->input('save_only')) {
            return response()->json(['success' => true]);
        }

        // 다음 문제로 이동
        $nextIndex = $currentIndex + 1;
        Session::put('quiz_current', $nextIndex);

        // 모든 문제를 풀었으면 결과 페이지로
        if ($nextIndex >= count($questionIds)) {
            return redirect()->route('questions.result');
        }

        return redirect()->route('questions.solve');
    }

    /**
     * 이전 문제로 이동
     */
    public function previous()
    {
        $currentIndex = Session::get('quiz_current', 0);

        if ($currentIndex > 0) {
            Session::put('quiz_current', $currentIndex - 1);
        }

        return redirect()->route('questions.solve');
    }

    /**
     * 결과 페이지
     */
    public function result()
    {
        $questionIds = Session::get('quiz_questions', []);
        $userAnswers = Session::get('quiz_answers', []);
        $category = Session::get('quiz_category');
        $startTime = Session::get('quiz_start_time');

        if (empty($questionIds)) {
            return redirect()->route('questions.index');
        }

        $questions = Question::whereIn('id', $questionIds)
            ->orderBy('no')
            ->get();

        $correctCount = 0;
        $wrongCount = 0;
        $unanswered = 0;

        foreach ($questions as $question) {
            $userAnswer = $userAnswers[$question->id] ?? null;

            if ($userAnswer === null) {
                $unanswered++;
            } elseif ($userAnswer == $question->answer) {
                $correctCount++;
            } else {
                $wrongCount++;
            }
        }

        $totalQuestions = count($questions);
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 1) : 0;

        // 소요 시간 계산
        $duration = $startTime ? now()->diffInMinutes($startTime) : 0;

        return view('questions.result', compact(
            'questions',
            'userAnswers',
            'correctCount',
            'wrongCount',
            'unanswered',
            'score',
            'totalQuestions',
            'category',
            'duration'
        ));
    }

    /**
     * 문제 풀기 세션 초기화
     */
    public function reset()
    {
        Session::forget([
            'quiz_questions',
            'quiz_current',
            'quiz_answers',
            'quiz_category',
            'quiz_start_time'
        ]);

        return redirect()->route('questions.index');
    }

    /**
     * 특정 문제로 바로 이동
     */
    public function jump($index)
    {
        $questionIds = Session::get('quiz_questions', []);

        if ($index >= 0 && $index < count($questionIds)) {
            Session::put('quiz_current', $index);
        }

        return redirect()->route('questions.solve');
    }

    /**
     * 특정 교시로 이동
     */
    public function jumpToClass($classTime)
    {
        $classInfo = Session::get('quiz_class_info', []);

        if (isset($classInfo[$classTime])) {
            Session::put('quiz_current', $classInfo[$classTime]['start_index']);
        }

        return redirect()->route('questions.solve');
    }
}