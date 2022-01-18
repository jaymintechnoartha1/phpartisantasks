<?php
#/App/Console/Commands/QuizStart.php
namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $menu = array();
        $menu[0] = array("key" => 'Stats of users question' ,"value" => "php artisan quiz:on {username} - Will create users if not and will show stats of usres questions");
        $menu[1] = array("key" => 'Create question', "value" => "php artisan question:create {questionname} {answer}  - Question will create with answers");
        $menu[2] = array("key" => 'Practice your quiz', "value" => "php artisan quiz:practice {username} {question_id} - Answer any quiz");
        $menu[3] = array("key" => 'Questions and Answers List', "value" => "php artisan question:list - Question and answers of quiz");
        $menu[4] = array("key" => 'Reset users', "value" => "quiz:reset {username}  - Will Reset and Remove all users data from quiz and users table respectively");
        /*

1. php artisan question:dashboard - Will Show dashboard of what users can do
2. php artisan quiz:on {username} - Will create users if not and will show stats of usres questions

3. php artisan question:create {questionname} {answer}  - Question will create with answers
4. php artisan question:list - Question and answers of quiz
5. php artisan quiz:practice {username} {question_id} - Answer any quiz
6. quiz:reset {username}  - Will Reset and Remove all users data from quiz and users table respectively

*/

        $this->line("Your options are :");
        foreach ($menu as $key => $value) {
            $this->line($value['key']." -  ". $value['value']);
        }
    }
}
