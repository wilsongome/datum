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

## Sobre

Essa aplicação possui uma rota que recebe uma string, e irá concatenar uma chave aleatória na string informada, e então irá gerar HASH MD5 disso. Ela seguirá fazendo isso até que seja gerado um hash iniciando por 4 zeros '0000', exemplo: 0000f7ed7e54e5fc085edf447e8bcc27<br />

Ao encontrar essa ocorrência, ela irá retornar um JSON contendo a CHAVE, o HASH e o número de tentativas, conforme abaixo:<br />

```{"key_found":"eNr4XokY","hash":"00002727db2c47b9f6cb108f6a86a635","tries":35016}```

## Configurar ambiente
Para executar a aplicação você precisará:
1) Clonar o repostório da aplicação no diretório do Apache ou outro de sua preferência
2) ```cd /path/to/app``` Navegar até o diretório da aplicação 
3) ```composer install``` Para instalar todas as dependências
4) Ter o banco de dados MySQL em execução, com um usuário com permissão GRANT para criar as tabelas da aplicação.
5) ```cp .env.example .env``` Criar o arquivo .ENV da aplicação, e cofigurar as informações de acesso ao banco de dados.
6) ```php artisan migrate``` Para criar as tabelas da aplicação (Caso o banco definido no .ENV não exista, ele pergutará se quer criar, responda YES)
7) Configurar o virtual host do apache apontando para a o diretório public da aplicação.
    a) Alternativamente, você poderá também utilizar o serviço do próprio PHP ou do Artisan<br />
    ```php -S localhost:8080 -t public``` <br />
    ou <br />
    ```php artisan serv```
8) Agora basta acessar as **rotas** conforme instruções abaixo

## Rotas 

**GET**
http://localhost/api/hash/generate/{string}<br />
Retorna 200, e um JSON em caso de sucesso.<br />
```{"key_found":"eNr4XokY","hash":"00002727db2c47b9f6cb108f6a86a635","tries":35016}```<br />

Em caso de falha, vai retornar o código de erro e um JSON vazio


**GET**
http://localhost/api/hash/results/{page}?tries=N<br />
Retorna 200, e um JSON contendo a lista de resultado em caso de sucesso.<br />
O resultado está paginado em 20 itens, e o parâmetro **page** (path) da rota é obrigatório.<br />
A rota também recebe um parâmetro opcional **tries** (query string, ex: /?tries=N). Esse parâmetro irá filtrar os resultados onde as tentativas de resolução forem MENORES que o número informado.<br />
A ordenação está seguindo a ordem de entrada no banco de dados.<br />
Exemplo de retorno contendo 2 resultados:<br />
```[{"batch":"2023-07-14 13:12:17","order_number":1,"str_in":"teste","key_found":"R78n2WOi"},{"batch":"2023-07-14 13:12:17","order_number":2,"str_in":"00008a38c1f0c5d8de967c3b14a44db7","key_found":"LuSf8D4V"}]```<br />

Em caso de falha, vai retornar o código de erro e um JSON vazio (Exemplo 400, para parâmetros inválidos)
