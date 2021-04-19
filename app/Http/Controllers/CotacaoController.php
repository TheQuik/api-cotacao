<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \App\Models\CotacoesRegister;


class CotacaoController extends Controller
{
    public function moedas(){
        $link = "https://economia.awesomeapi.com.br/xml/available";
        $moedas = file_get_contents($link);
        $listaMoedas = json_encode(simplexml_load_string($moedas));
        $return = json_decode($listaMoedas,true);
        asort($return);
        return response()->json($return, 200);
    }

    /**
     * Buscando a cotação e salvando no banco o registro do usuário
     *
     * @return void
     */
    public function cotacao(){
        $link = "https://economia.awesomeapi.com.br/last/".request()->coin;
        $cotacao = file_get_contents($link);
        $key = str_replace("-", "", request()->coin);
        $log = (json_decode($cotacao))->$key;
        try {
            $save = new CotacoesRegister();
            $save->ask = $log->ask;
            $save->bid = $log->bid;
            $save->code = $log->code;
            $save->codein = $log->codein;
            $save->high = $log->high;
            $save->low = $log->low;
            $save->name = $log->name;
            $save->pctChange = $log->pctChange;
            $save->timestamp = $log->timestamp;
            $save->user_id = auth()->user()->id;
            $save->varBid = $log->varBid;
            $save->save();
        } catch (\Throwable $th) {
            return response()->json($th->getMessage() , 500);
        }
        return response()->json($log, 200);
    }
}
