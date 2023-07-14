<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## PHP (Version)

PHP 8.1.2-1

## MySQL (Version)

8.0.33-0ubuntu0.22.04.2

## SO (Version)

Linux Ubuntu 22.04

## About Application

Essa aplicação possui uma rota que recebe uma string, e irá concatenar uma chave aleatória na string informada, e então gerar HASH MD5 disso. Ela seguirá fazendo isso até que seja gerado um hash iniciando por 4 zeros '0000', exemplo: 0000f7ed7e54e5fc085edf447e8bcc27

Ao encontrar essa ocorrência, ela irá retornar um JSON contendo a CHAVE, o HASH e o número de tentativas, conforme abaixo:

```{"key_found":"eNr4XokY","hash":"00002727db2c47b9f6cb108f6a86a635","tries":35016}```

## Rotas 

**GET**
http://localhost/api/hash/generate/{string}
Retorna 200, e um JSON em caso de sucesso
```{"key_found":"eNr4XokY","hash":"00002727db2c47b9f6cb108f6a86a635","tries":35016}```

Em caso de falha, vai retornar o código de erro e um JSON vazio


**GET**
http://localhost/api/hash/results/{page}?tries=N
Retorna 200, e um JSON contendo a lista de resultado em caso de sucesso.
O resultado está paginado em 20 itens, e o parâmetro **page** (path) da rota é obrigatório
A rota também recebe um parâmetro opcional **tries** (query string, ex: /?tries=N). Esse parâmetro irá filtrar os resultados onde as tentativas de resolução forem MENORES que o número informado.
A ordenação está seguindo a ordem de entrada no banco de dados

Em caso de falha, vai retornar o código de erro e um JSON vazio (Exemplo 400, para parâmetros inválidos)
