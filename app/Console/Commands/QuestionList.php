<?php
#/App/Console/Commands/QuizStart.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class QuestionList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:list';

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
        $questions = DB::select('select * from questions');
        $this->line("Your questions are ");
        foreach ($questions as $key => $value) {
            $this->line($value->question." -- Answer: ".$value->answer);
        }
    }
}
