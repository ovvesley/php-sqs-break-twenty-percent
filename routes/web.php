<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Models\Resposta;
use App\Jobs\EncontrarRespostaJob;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/api/encontrar-resposta', function () use ($router) {
    $resposta = new Resposta();
    $resposta->save();
    dispatch(new EncontrarRespostaJob($resposta));
    return ['status'=>'ok', 'messagem'=>'item adicionado na fila para processamento', 'id'=> $resposta->id];
});


$router->get('/api/encontrar-resposta/{id}', function (int $id) use ($router) {
    $resposta = Resposta::findOrFail($id);
    $response = $resposta->response? json_decode($resposta->response, true): [];

    return array_merge(
        [
            'id'=>$resposta->id,
            'status' => $resposta->status
        ],
        $response
    );
});
