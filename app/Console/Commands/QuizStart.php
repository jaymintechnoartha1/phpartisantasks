<?php
#/App/Console/Commands/QuizStart.php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class QuizStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:start {user} {age} {--difficulty=} {--istest=}';

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
        $difficulty =  $this->option('difficulty');

        if(!$difficulty){
            $difficulty = 'easy';
        }

        $this->line('Welcome '.$this->argument('user').", starting test in difficulty : ". $difficulty);

        $questions = [
            'easy' => [
                'How old are you ?', "What is the name of your mother?",
                'Do you have 3 parents ?','Do you like Javascript?',
                'Do you know what is a JS promise?'
            ],
            'hard' => [
                'Why the sky is blue?', "Can a kangaroo jump higher than a house?",
                'Do you think i am a bad father?','why the dinosaurs disappeared?',
                "why don't whales have gills?"
            ]
        ];

        $questionsToAsk = $questions[$difficulty];
        $answers = [];

        foreach($questionsToAsk as $question){
            $answer = $this->ask($question);
            array_push($answers,$answer);
        }

        $this->info("Thanks for do the quiz in the console, your answers : ");

        for($i = 0;$i <= (count($questionsToAsk) -1 );$i++){
            $this->line(($i + 1).') '. $answers[$i]);
        }
    }
}
