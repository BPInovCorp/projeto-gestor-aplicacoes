<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class ViesService
{
    private const BASE_URL = 'https://ec.europa.eu/taxation_customs/vies/rest-api/ms';

    public function validar(string $codigoPais, string $nif): array
    {
        $codigoPaisLimpo = strtoupper(trim($codigoPais));
        $nifLimpo = preg_replace('/[^A-Za-z0-9]/', '', $nif) ?? '';

        if (empty($codigoPaisLimpo) || empty($nifLimpo)) {
            return [
                'valido' => false,
                'mensagem' => 'Código de país ou NIF inválido.',
            ];
        }

        try {
            $response = Http::timeout(8)
                ->acceptJson()
                ->get(self::BASE_URL . "/{$codigoPaisLimpo}/vat/{$nifLimpo}");

            if ($response->failed()) {
                return [
                    'valido' => false,
                    'mensagem' => 'O serviço VIES encontra-se indisponível de momento.',
                ];
            }

            $dados = $response->json();
            $valido = (bool) ($dados['isValid'] ?? false);

            if (!$valido) {
                return [
                    'valido' => false,
                    'mensagem' => 'O NIF indicado não é válido ou não se encontra registado no VIES.',
                ];
            }

            $moradaCompleta = $dados['address'] ?? '';
            $endereco = $this->processarMorada($moradaCompleta, $codigoPaisLimpo);

            return [
                'valido' => true,
                'nome' => $dados['name'] !== '---' ? ($dados['name'] ?? null) : null,
                'morada' => $endereco['morada'],
                'codigo_postal' => $endereco['codigo_postal'],
                'localidade' => $endereco['localidade'],
                'mensagem' => 'NIF validado com sucesso.',
            ];
        } catch (Throwable) {
            return [
                'valido' => false,
                'mensagem' => 'Não foi possível estabelecer ligação ao serviço VIES.',
            ];
        }
    }

    private function processarMorada(string $moradaBruta, string $codigoPais): array
    {
        $moradaBruta = trim($moradaBruta);
        if (empty($moradaBruta) || $moradaBruta === '---') {
            return [
                'morada' => null,
                'codigo_postal' => null,
                'localidade' => null,
            ];
        }

        $linhas = array_values(array_filter(array_map('trim', explode("\n", $moradaBruta))));

        if ($codigoPais === 'PT') {
            $codigoPostal = null;
            $localidade = null;
            $linhasMorada = [];

            foreach ($linhas as $linha) {
                if (preg_match('/(\d{4}-\d{3})(?:\s+(.+))?/', $linha, $matches)) {
                    $codigoPostal = $matches[1];
                    if (!empty($matches[2])) {
                        $localidade = trim($matches[2]);
                    }
                } else {
                    $linhasMorada[] = $linha;
                }
            }

            return [
                'morada' => !empty($linhasMorada) ? implode(', ', $linhasMorada) : null,
                'codigo_postal' => $codigoPostal,
                'localidade' => $localidade,
            ];
        }

        return [
            'morada' => implode(', ', $linhas),
            'codigo_postal' => null,
            'localidade' => null,
        ];
    }
}