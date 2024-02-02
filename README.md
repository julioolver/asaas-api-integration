# Projeto para integração do asaas com PHP e Laravel

Este projeto Laravel é uma aplicação robusta que segue as melhores práticas de design e arquitetura de software, aplicando SOLID, clean code, calistenia de objetos, entre outros conceitos e patterns conhecidos (como Adapters e Factory Pattern). O projeto utiliza um padrão de design com Services e Repositories para abstrair a lógica de negócios e a camada de acesso a dados. Além disso, conta com Factories, DTOs, Enums, Contacts/Interfaces e testes unitários e de feature.
A integração com APIs externas é gerenciada no diretório de Integrações, e os testes unitários e de feature garantem a confiabilidade e qualidade do código entre camadas.

## Estrutura do Projeto

Dentro do diretório `App`, o projeto inclui:

- **DTO (Data Transfer Objects)**: Utilizados para transferir dados entre camadas da aplicação.
- **Enums**: Define conjuntos de constantes nomeadas para melhorar a legibilidade e a manutenção do código.
- **Factory**: Utilizado para criar instâncias de objetos de uma maneira fácil e limpa, útil para trabalhar com várias integrações de aplicações externas, em conjunto com adapters.
- **Services**: Contém a lógica de negócios e as regras da aplicação.
- **Repositories**: Fornece uma camada de abstração sobre o acesso a dados, permitindo uma maneira mais flexível de interagir com o banco de dados. Também, existe no diretório os contracts (interfaces).
- **Config**: Diretório para arquivos de configuração da aplicação.
- **Integrations**: Pasta dedicada para integrações com APIs externas (adapters), como AsaaS, Pagar.me, mantendo essa lógica separada do restante do código da aplicação. Dentro dela, existe também os contracts (interfaces), para aplicar a inversão da dependência (SOLID)

## Configuração e Instalação

A seguir, passo a passo para rodar o sistema em ambiente local.

### Pré-requisitos

Instruções para configurar e instalar o projeto:

-   Docker configurado no sistema operacional

-   Fazer uma cópia do .env.exemple para .env, preenchendo com as credenciais obrigatórias (asaas_key).

### Instalação

Para emular o ambiente de desenvolvimento, foi utilizado o Docker e Docker-compose, em conjunto com o Sail do Laravel.

```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

ou 

```
docker-compose up --build
```

se preferir (após instalar as dependências do projeto):

```
./vendor/bin/sail up
```

Para criar as tabelas do sistema no banco de dados, basta executar o comando:

```
./vendor/bin/sail artisan migrate
```

Caso seja necessário instalar as dependências novamente:

```
./vendor/bin/sail composer install
```

Após isso, o sistema se torna acessível via:

`http://localhost:8085`

## Rodar documentação da API

Certifique-se que todas as dependências do projeto estejam atualizadas e instaladas:

```
./vendor/bin/sail composer install
```

Após isso, acesse a seguinte URL, onde até o momento existe a documentação via Swagger dos customers:

`http://localhost:8085/api/doc#/`

## Rodando os testes

Para rodar os testes criados, basta executar:

```
./vendor/bin/sail artisan test
```

## TODO

Deixo anotado aqui, com um TODO para ser feito ainda dentro deste projeto:
- Adicionar Swagger para documentação das rotas de entrada e saída de pagamentos (Customers já existe - em andamento)
- Hoje estou reotnrando os erros da API externa em um json stringfy, criar exception do Laravel para retornar um JSON formatado.
## Autores

* **Julio Cesar Oliveira da Silva**