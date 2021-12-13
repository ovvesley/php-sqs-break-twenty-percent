<?php

namespace App\Jobs;


use App\Models\Resposta;
use App\Service\EncontrarResposta;

class EncontrarRespostaJob extends Job
{

    private $resposta;
    public $tries = 3;
    private const MAX_NUMBER_RAND = 20;
    private const EXCEPTION_NUMBER_SMALL_THEN = 4;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Resposta $resposta)
    {
        $this->resposta = $resposta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->resposta->update(['status' => 'processing']);
        $encontrarResposta = new EncontrarResposta();
        $answer = $encontrarResposta->handle();

        $rand = rand(1, self::MAX_NUMBER_RAND);
        sleep($rand);

        if ($rand < self::EXCEPTION_NUMBER_SMALL_THEN){
            $messageError = "Exception!! Number=".$rand. " Answer = ".$answer;
            throw \Exception($messageError);
        }

        $this->resposta->update(['status' => 'success', 'response'=> ['answer' => $answer, 'timerand' => $rand]]);
    }
}
