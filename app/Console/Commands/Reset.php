<?php
#/App/Console/Commands/QuizStart.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\User as User;
use App\Question as Question;
use App\Quiz as Quiz;

class Reset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:reset {username} ';

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
            $prompt = $this->ask("Are you sure you want to remove this user & it's quiz data? (Type y for yes and n for no)");
            if($prompt == "y")
            {
                $user_id = $users->id;
                User::where('id',$user_id)->delete();
                Quiz::where('username',$user_id)->delete();
                $this->line("User details has been removed permanently.");
            }
        }
    }
}
