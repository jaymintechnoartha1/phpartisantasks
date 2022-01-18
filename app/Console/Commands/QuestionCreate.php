<?php
#/App/Console/Commands/QuizStart.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class QuestionCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:create {questionname} {answer} ';

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
        $this->line('Welcome');
        $questions = [];
        $question = $this->ask("What is question?");
        array_push($questions,$question);
        $answers = [];
        $answer = $this->ask("What is answer?");
        array_push($answers,$answer);
        $this->info("Thanks your inputted data are : ");


        foreach ($questions as $key => $value) {
            $data['question'] = $value;
            $data['answer'] = $answers[$key];
            
            DB::table('questions')->insert($data);
            $this->line("Your question was ".$value." and answer was ". $answers[$key]);
        }
    }
}
