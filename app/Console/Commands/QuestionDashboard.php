<?php
#/App/Console/Commands/QuizStart.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\User as User;
use App\Question as Question;
use App\Quiz as Quiz;

class QuestionDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:dashboard';

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
        /*$menu = array();
        $menu[0] = array("key" => 'Stats of users question' ,"value" => "php artisan quiz:on {username} - Will create users if not and will show stats of usres questions Press 1");
        $menu[1] = array("key" => 'Create question', "value" => "php artisan question:create {questionname} {answer}  - Question will create with answers Press 2");
        $menu[2] = array("key" => 'Practice your quiz', "value" => "php artisan quiz:practice {username} {question_id} - Answer any quiz Press 3");
        $menu[3] = array("key" => 'Questions and Answers List', "value" => "php artisan question:list - Question and answers of quiz Press 4");
        $menu[4] = array("key" => 'Reset users', "value" => "quiz:reset {username}  - Will Reset and Remove all users data from quiz and users table respectively Press 5");*/
        $menu1[] = array('option_number' => '1','description' => 'Will create users if not and will show stats of usres questions'); 
        $menu1[] = array('option_number' => '2','description' => 'Question will create with answers'); 
        $menu1[] = array('option_number' => '3','description' => 'Answer any quiz'); 
        $menu1[] = array('option_number' => '4','description' => 'Question and answers of quiz');
        $menu1[] = array('option_number' => '5','description' => 'Reset and Remove all users data from quiz and users table respectively');  
        $menu1[] = array('option_number' => '6','description' => 'Type exit to exit from console');  

        /*$this->line("Your options are :");
        foreach ($menu as $key => $value) {
            $this->info($value['key']." -  ". $value['value']);
        }*/

        $this->table(
                ['Option Number','Description'],
                $menu1
            );
        $option = $this->ask("Please enter number");
        $this->operations($option);
    }

    public function operations($option)
    {
        if($option == 1)
        {
            //php artisan quiz:on {username}
            $username = $this->ask("Please enter username");
            $users = User::where('username',$username)->first();
            //print_r($users->username); die;
            if($users === null)
            {
                $this->info("No username found, creating new");
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
                $this->info("Found username".$users->id);
                $user_id = $users->id;
            }

            
            
            $quiz = Quiz::select(
                                "quiz.id as quiz_id", 
                                //"quiz.username",
                                "quiz.status", 
                                "questions.question"
                            )
                            ->leftJoin("questions", "questions.id", "=", "quiz.question_id")
                            ->where('quiz.username',$user_id)
                            ->get();
            //dd($quiz);
            $this->info("Staistics of your quiz: ");
            //print_r($quiz; die;
            $this->table(
                ['Question id', 'Status','Question'],
                $quiz->toArray()
            );
            /*foreach ($quiz as $key => $value) {
                $this->line("Question id ==".$value->quiz_id." Question ==".$value->question." ==STATUS== ".$value->status);
            }*/

            //Fetch as Footer to show statistcs of user
            $matchThese = ['username' => $user_id, 'status' => 'correct'];
            $correctquestions = Quiz::where($matchThese)->get();
            $total_question = $quiz->count();
            $correct_question = $correctquestions->count();
            if($total_question != 0)
            {
                $updated_cal = ($correct_question / $total_question) * 100;
                $this->info("Stats = ".$updated_cal."% Completed");
            }
            else
            {
                $this->info("Stats = 0% Completed");   
            }
            $option = $this->ask("Please enter number");
            $this->operations($option);
        }
        else if($option == 2)
        {
            $questions = [];
            $question = $this->ask("What is question?");
            array_push($questions,$question);
            $answers = [];
            $answer = $this->ask("What is answer?");
            array_push($answers,$answer);
            $this->info("Thanks your inputted data are : ");

            $main_arr = [];
            $main_arr[0] = $question;
            $main_arr[1] = $answer;
            foreach ($questions as $key => $value) {
                $data['question'] = $value;
                $data['answer'] = $answers[$key];
                
                DB::table('questions')->insert($data);
                $question_id = DB::select('SELECT LAST_INSERT_ID() as last_id');
                $last_id = $question_id[0]->last_id;
                //$this->line("Your question was ".$value." and answer was ". $answers[$key]);
            }

            //Assign latest created question to all users
            $users = User::all();
            // print_r($users); die;
            foreach ($users as $u) 
            {
                $values1['username'] = $u->id;
                $values1['question_id'] = $last_id;
                $values1['status'] = "Not answered";
                DB::table('quiz')->insert($values1);
            }
            
            
            $this->table(
                ['Question','Answer'],
                [$main_arr]
            );
            $option = $this->ask("Please enter number");
            $this->operations($option);
        }
        else if($option == 3)
        {
            $username = $this->ask('Please enter username');
            $question_id = $this->ask('Please enter question_id');
            //actually it is quiz_id
            $users = User::where('username',$username)->first();
            //print_r($users[0]->username); die;
            if($users === null)
            {
                $this->info("No username found, creating new");
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
                $this->info("Found username".$users->id);
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

                $this->info($ans." answer");
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
                $this->info("Stats = ".$updated_cal."% Completed");
            }
            else
            {
                $this->info("No quiz is active with this username");
            }
            $option = $this->ask("Please enter number");
            $this->operations($option);
        }
        else if($option == 4)
        {
            $questions = Question::select('question','answer')->get();
            //print_r($questions->toArray()); die;
            /*$this->line("Your questions are ");
            foreach ($questions as $key => $value) {
                $this->line($value->question." -- Answer: ".$value->answer);
            }*/
            $this->table(
                ['Question','Answer'],
                $questions->toArray()
            );
            $option = $this->ask("Please enter number");
            $this->operations($option);
        }
        else if($option == 5)
        {
            $username = $this->ask('username');
        
            //actually it is quiz_id
            $users = User::where('username',$username)->first();
            //print_r($users[0]->username); die;
            if($users === null)
            {
                $this->info("Sorry,no records found for this username");
            }
            else
            {
                $prompt = $this->ask("Are you sure you want to remove this user & it's quiz data? (Type y for yes and n for no)");
                if($prompt == "y")
                {
                    $user_id = $users->id;
                    User::where('id',$user_id)->delete();
                    Quiz::where('username',$user_id)->delete();
                    $this->info("User details has been removed permanently.");
                }
            }
            $option = $this->ask("Please enter number");
            $this->operations($option);
        }
        else if($option == "exit")
        {
            $this->info("Quitting Console, Good-Bye!");
        }
    }
}
