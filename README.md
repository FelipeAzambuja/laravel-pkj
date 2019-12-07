# Bem Vindo ao Laravel-pkj
O pacote oferece recursos de rotas automáticas , funções de fácil acesso ao query builder do Laravel e envio de formulários por ajax .

# Instalando em seu projeto
Primeiro instale a dependência por composer rodando 

```bash
composer require felipeaz/laravel-pkj
```

Após instalado rode o comando 
Adicione no seu arquivo **config/app.php** procure o array **providers**  e adicione a classe do **provider**.

```php
'providers' => [
    //...
    FelipeAzambuja\PKJServiceProvider::class,
]
```

# Rotas Automáticas

Para agilizar o processo de roteamento no Laravel este projeto inclui o recurso de rotas automáticas ,que cria as rotas de maneira padronizada.

![image-20191207151455937](C:\inetpub\wwwroot\laravel-pkj-work\packages\laravel-pkj\src\readme1.png)

As rotas são declaradas conforme o nome do Controller e método, o mesmo vale para o [caffeined modules](https://github.com/caffeinated/modules) 


# Fácil Acesso ( Query Builder )

O Laravel-pkj inclui diversas funções para facilitar o acesso ao query builder.

## db($name = 'mysql')

Illuminate\Support\Facades\DB::connection

## unprepared

Apelido para Illuminate\Support\Facades\DB::unprepared

## schema($name = 'mysql')

Apelido para \Illuminate\Support\Facades\Schema::connection

## begin

Apelido para Illuminate\Support\Facades\DB::beginTransaction

## rollback

Apelido para Illuminate\Support\Facades\DB::rollBack

## commit

Apelido para Illuminate\Support\Facades\DB::commit

## table($table, $as = null)

Apelido para Illuminate\Support\Facades\DB::table

## raw

Apelido para Illuminate\Support\Facades\DB::raw


# Chamadas Ajax

Com o pacote se torna fácil a chamada de ajax.

## Executando Javascript no Controller




Este projeto é possivel graças ao apoio de Paola o meu amorzin <3
