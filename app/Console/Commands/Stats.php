<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\User as User;
use App\Question as Question;
use App\Quiz as Quiz;

class Stats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:stats {username}';

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
        
        //actually it is quiz_id
        $users = User::where('username',$username)->first();
        //print_r($users[0]->username); die;
        if($users === null)
        {
            $this->line("Sorry,no records found for this username");
        }
        else
        {
            //$this->line("Found username".$users->id);
            $user_id = $users->id;

            //Fetch as Footer to show statistcs of user
            $matchThese = ['username' => $user_id, 'status' => 'correct'];
            $correctquestions = Quiz::where($matchThese)->get();

            //$matchThese1 = ['username' => $user_id];
            $matchThese1 = "(username = ".$user_id.") and (status = 'correct' or status = 'incorrect')";
            $questions_answered = DB::table('quiz')->selectRaw("*")->whereRaw($matchThese1)->get();
            // echo $questions_answered;
            // die;
            $questions_answered_count = $questions_answered->count();
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

            $updated_cal_answered = ($questions_answered_count / $total_question) * 100;
            $this->line($total_question." = The total amount of questions");
            $this->line("Stats = ".$updated_cal."% has correct answer");
            $this->line("Stats = ".$updated_cal_answered."% has been answered");
        }
    }
}
