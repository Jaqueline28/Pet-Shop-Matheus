<?php

include_once('../../rotas.php');
include_once($funcoesRoute);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css-dinamico/estilo.css">
    <title>Cadastro - Hantaro PetShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="../css-estatico/header.css">
    <link rel="stylesheet" href="../css-dinamico/cadastro-cliente.css">
</head>

<body>
    <header>
        <a href="<?php echo $homeRoute; ?>" id="logo">
            <p>Hamtaro Petshop</p>
        </a>

        <div class="acesso-seguro">
            <p>Ambiente Seguro</p>
            <img src="../img-dinamico/cadeado.svg" alt="">
        </div>
    </header>

    <?php
    if (isset($_SESSION['msgCadCli'])) { // Verifica se há uma mensagem para mostrar
        echo $_SESSION['msgCadCli'];
        unset($_SESSION['msgCadCli']);
    }
    ?>

    <div id="pi" class="container">
        <div class="titulo">
            <h1>Criar um conta</h1>
            <p>Complete os campos com as suas informações</p>
        </div>

        <form action="<?php echo $procCadCliRoute; ?>" method="post" id="pai">

            <div id="base" class="col-lg-5 m-4">
                <div class="input row">
                    <label for="nome">NOME *</label>
                    <input type="text" name="nome" placeholder="Digite o seu nome" required autofocus><br><br>
                </div>

                <div class="input row">
                    <label for="sobrenome">SOBRENOME *</label>
                    <input type="text" name="sobrenome" placeholder="Digite o seu sobrenome" required><br><br>
                </div>

                <div class="input row">
                    <label for="cpf">CPF *</label>
                    <input type="text" name="cpf" pattern="\d{3}[.]?\d{3}[.]?\d{3}[-]?\d{2}"
                        placeholder="Digite seu CPF" required><br><br>

                </div>

                <div class="input row">
                    <label for="celular">CELULAR *</label>
                    <input type="text" name="celular" pattern="[(]?[0-9]{2}[)]?[0-9]{5}[-]?[0-9]{4}" placeholder="Digite seu telefone"
                        require><br><br>

                </div>

                <div class="input row">
                    <label for="email">EMAIL *</label>
                    <input type="email" name="email" placeholder="Digite seu email para login" required><br><br>

                </div>

                <div class="input row">
                    <label for="senha">SENHA *</label>
                    <input type="password" name="senha" pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^\w\s]).{8,}$"
                        placeholder="Digite sua senha" required><br><br>

                </div>

                <div class="input row">
                    <label for="senha">CONFIRMAR SENHA *</label>
                    <input type="password" name="confsenha" pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[^\w\s]).{8,}$"
                        placeholder="Digite sua senha" required><br><br>

                </div>

            </div>

            <div id="endereco" class="col-lg-5 m-4">

                <div class="input row">
                    <label>CEP *</label>
                    <input type="text" name="cep" pattern="[0-9]{8}" id="cep" placeholder="Digite o CEP"
                        onblur="pesquisacep(this.value)" required><br><br>

                </div>

                <div class="input row">
                    <label>RUA *</label>
                    <input type="text" name="log" id="log" placeholder="Digite sua rua" required readonly><br><br>

                </div>

                <div class="input row">
                    <label>NÚMERO *</label>
                    <input type="text" name="numero" id="numero" placeholder="Digite o número da casa" pattern="\d{1,5}"
                        required><br><br>

                </div>

                <div class="input row">
                    <label>COMPLEMENTO</label>
                    <input type="text" name="complemento" id="complemento" placeholder="Digite o complemento"
                        value=""><br><br>

                </div>

                <div class="input row">
                    <label>BAIRRO *</label>
                    <input type="text" name="bairro" id="bairro" placeholder="Digite o bairro" required
                        readonly><br><br>

                </div>

                <div class="input row">
                    <label>CIDADE *</label>
                    <input type="text" name="cid" id="cid" placeholder="Digite a cidade" required readonly><br><br>

                </div>

                <div class="input row">
                    <label>UF *</label>
                    <input type="text" name="uf" pattern="[a-zA-Z]{2}" id="uf" placeholder="Digite a uf" required
                        readonly><br><br>

                </div>



            </div>

            <div class="botoes-cadastro">
                <p id="senhanaoigual"></p>
                <input type="submit" value="Cadastrar" id="cadastrar" disabled>

                <?php
                if (isset($_SESSION['tipo'])) { // Verifica se o usuário é um funcionário
                    // se for o botão voltar leva pra página de agendamentos
                    echo "<a href = " . $agendamentoFunRoute . ">Voltar</a>";
                } else {
                    // se não o botão voltar leva pra home
                    echo "<a href = " . $homeRoute . ">Voltar</a>";
                }
                ?>
            </div>

        </form>

    </div>



    <script src="<?php echo $viacepRoute; ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
    <script src="<?php echo $functionsRoute; ?>"></script>
    <script src="<?php echo $confSenhaRoute; ?>"></script>
</body>

</html>
