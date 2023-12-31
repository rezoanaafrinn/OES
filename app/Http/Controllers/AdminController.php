<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QnaExam;

class AdminController extends Controller
{
    // Add subject
    public function addSubject(Request $request)
    {
        
        try{
            Subject::insert([
                'subject' => $request->subject,
                'category' => $request->category
            ]);

            return response()->json(['success'=>true,'msg'=>'Subject Added Successfully!']); 
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        };
         
    }

    // Edit Subject
    public function editSubject(Request $request)
    {
        
        try{
            
            $subject = Subject::find($request->id);
            $subject->subject = $request->subject;
            $subject->category = $request->category;
            $subject->save();

            return response()->json(['success'=>true,'msg'=>'Subject Updated Successfully!']); 
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        };
         
    }

    // Delete Subject
    public function deleteSubject(Request $request)
    {
        
        try{
            Subject::where('id',$request->id)->delete();

            return response()->json(['success'=>true,'msg'=>'Subject Deleted Successfully!']); 
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        };
         
    }

    // Exam dashboard load
    public function examDashboard()
    {
        $subjects = Subject::all();
        $exams = Exam::with('subjects')->get();
        return view('admin.exam-dashboard',['subjects'=>$subjects,'exams'=>$exams]);
    }

    // Add exam
    public function addExam(Request $request)
    {
        try{
            Exam::insert([
                'exam_name'=> $request->exam_name,
                'subject_id'=> $request->subject_id,
                'date' => $request->date,
                'time' => $request->time,
                'attempt' => $request->attempt

            ]);
            return response()->json(['success'=>true,'msg'=>'Exam added Successfully!']); 
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        };
    }

    public function getExamDetail($id)
    {
        try{
            $exam = Exam::where('id',$id)->get();
            return response()->json(['success'=>true,'data'=>$exam]); 
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        };
    }

    public function updateExam(Request $request)
    {
        try{
            $exam = Exam::find($request->exam_id);
            $exam->exam_name = $request->exam_name;
            $exam->subject_id = $request->subject_id;
            $exam->date = $request->date;
            $exam->time = $request->time;
            $exam->attempt = $request->attempt;
            $exam->save();
            return response()->json(['success'=>true,'msg'=>'Exam Updated successfully']); 
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        }; 
    }

    // Delete exam

    public function deleteExam(Request $request)
    {
        try{
            Exam::where('id',$request->exam_id)->delete();
            return response()->json(['success'=>true,'msg'=>'Exam deleted successfull']);
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        };
    }

    public function qnaDashboard()
    {
        $questions = Question::with('answers')->get();
        return view('admin.qnaDashboard',compact('questions'));
    }


    // Add Q&A

    public function addQna(Request $request)
    {
        try{
            $questionId = Question::insertGetId([
                'question' => $request->question
            ]);

            foreach($request->answers as $answer){

                $is_correct = 0;
                if($request->is_correct == $answer){
                    $is_correct = 1;
                }
                Answer::insert([
                    'questions_id'=>$questionId,
                    'answer'=>$answer,
                    'is_correct'=>$is_correct
                ]);
            }

            return response()->json(['success'=>true,'msg'=>'Subject Deleted Successfully!']); 
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        };
    }

    public function getQnaDetails(Request $request)
    {
        $qna = Question::where('id',$request->qid)->with('answers')->get();

        return response()->json(['data'=>$qna]);
    }

    public function deleteAns(Request $request)
    {
        Answer::where('id',$request->id)->delete();
        return response()->json(['success'=>true,'msg'=>'Answer deleted successfully']);
    }

    public function updateQna(Request $request)
    {
        try{

            Question::where('id',$request->question_id)->update([
                'question' => $request->question
            ]);

            // old answer update
            if(isset($request->answers)){
                
                foreach($request->answers as $key => $value){

                    $is_correct = 0;
                    if($request->is_correct == $value){
                        $is_correct = 1;
                    }

                    Answer::where('id',$key)
                    ->update([
                        'questions_id' =>$request->question_id,
                        'answer' => $value,
                        'is_correct' => $is_correct
                    ]);
                }
            }

            // new answers added
            if(isset($request->new_answers)){
                foreach($request->new_answers as $answer){

                    $is_correct = 0;
                    if($request->is_correct == $answer){
                        $is_correct = 1;
                    }

                    Answer::inert([
                        'questions_id' =>$request->question_id,
                        'answer' => $answer,
                        'is_correct' => $is_correct
                    ]);
                }
            }

            return response()->json(['success'=>true,'msg'=>'Q&A updated successfully!']);

        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        }
    }

    //get questions
    public function getQuestions(Request $request)
    {
        try{
            $questions = Question::all();

            if(count($questions) > 0){
                $data = [];
                $counter = 0;

                foreach($questions as $question)
                {
                    $qnaExam = QnaExam::where(['exam_id'=>$request->exam_id,'question_id'=>$question->id])->get();
                    if(count($qnaExam)==0){
                        $data[$counter]['id'] = $question->id;
                        $data[$counter]['questions'] = $question->question;
                        $counter++;
                    }
                }
                return response()->json(['success'=>true,'msg'=>'Question data!','data'=>$data]);
            }
            else{
                return response()->json(['success'=>false,'msg'=>'Question not Found!']);
            }

        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        }
    }

    public function addQuestions(Request $request)
    {
        try{
            if(isset($request->questions_ids)){
              
                foreach($request->questions_ids as $qid){
                    QnaExam::insert([
                        'exam_id' => $request->exam_id,
                        'question_id' => $qid
                    ]);
                }
            }
            return response()->json(['success'=>true,'msg'=>'Questions added successfully!']);

        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        }
    }

    
}
