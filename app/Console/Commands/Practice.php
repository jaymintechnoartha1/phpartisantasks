<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\User as User;
use App\Question as Question;
use App\Quiz as Quiz;

class Practice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:practice {username} {question_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $username = $this->argument('username');
        $question_id = $this->argument('question_id');
        //actually it is quiz_id
        $users = User::where('username',$username)->first();
        //print_r($users[0]->username); die;
        if($users === null)
        {
            $this->line("No username found, creating new");
            $user['username'] = $username;
            
            DB::table('users')->insert($user);

            $values['username'] = $username;
            $users = User::where('username',$username)->first();
            $user_id = $users->id;
            //now insert all questions into quiz table
            $questions = Question::all();
            foreach ($questions as $key => $value) {
                //echo $value->id;
                $values1['username'] = $users->id;
                $values1['question_id'] = $value->id;
                $values1['status'] = "Not answered";
                DB::table('quiz')->insert($values1);
            }
        }
        else
        {
            $this->line("Found username".$users->id);
            $user_id = $users->id;
        }

        $matchThese1 = ['quiz.id' => $question_id,'username' => $user_id];
        $quiz = Quiz::select(
                            "quiz.id as quiz_id", 
                            "quiz.username",
                            "quiz.status", 
                            "questions.question",
                            "questions.answer"
                        )
                        ->leftJoin("questions", "questions.id", "=", "quiz.question_id")
                        ->where($matchThese1)
                        ->get();
        //dd($quiz);
        if(isset($quiz[0]) && $quiz[0]->answer!="")
        {

        
            //print_r($quiz; die;
            $correct_answer = $quiz[0]->answer;
            $user_answer = $this->ask($quiz[0]->question);
            //check answer and mark whatever correct,incorrect
            if($correct_answer == $user_answer)
            {
                $ans = "correct";
            }
            else
            {
                $ans = "incorrect";
            }

            $this->line($ans." answer");
            Quiz::where('id',$question_id)->update(['status'=>$ans]);
            
            //Fetch as Footer to show statistcs of user
            $matchThese = ['username' => $user_id, 'status' => 'correct'];
            $correctquestions = Quiz::where($matchThese)->get();
            
            $totalquiz_assign = Quiz::select(
                                "quiz.id as quiz_id", 
                                "quiz.username",
                                "quiz.status", 
                                "questions.question"
                            )
                            ->leftJoin("questions", "questions.id", "=", "quiz.question_id")
                            ->where('quiz.username',$user_id)
                            ->get();
            $total_question = $totalquiz_assign->count();
            
            $correct_question = $correctquestions->count();
            $updated_cal = ($correct_question / $total_question) * 100;
            $this->line("Stats = ".$updated_cal."% Completed");
        }
        else
        {
            $this->line("No quiz is active with this username");
        }
    }
}
