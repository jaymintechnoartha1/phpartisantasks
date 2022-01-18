<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\User as User;
use App\Question as Question;
use App\Quiz as Quiz;

class QuizOn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:on {username}';

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
        $users = User::where('username',$username)->first();
        //print_r($users->username); die;
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

        
        
        $quiz = Quiz::select(
                            "quiz.id as quiz_id", 
                            "quiz.username",
                            "quiz.status", 
                            "questions.question"
                        )
                        ->leftJoin("questions", "questions.id", "=", "quiz.question_id")
                        ->where('quiz.username',$user_id)
                        ->get();
        //dd($quiz);
        $this->line("Staistics of your quiz: ");
        //print_r($quiz; die;
        foreach ($quiz as $key => $value) {
            $this->line("Question id ==".$value->quiz_id." Question ==".$value->question." ==STATUS== ".$value->status);
        }

        //Fetch as Footer to show statistcs of user
        $matchThese = ['username' => $user_id, 'status' => 'correct'];
        $correctquestions = Quiz::where($matchThese)->get();
        $total_question = $quiz->count();
        $correct_question = $correctquestions->count();
        if($total_question != 0)
        {
            $updated_cal = ($correct_question / $total_question) * 100;
            $this->line("Stats = ".$updated_cal."% Completed");
        }
        else
        {
            $this->line("Stats = 0% Completed");   
        }
        
    }
}
