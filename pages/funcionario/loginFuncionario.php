<?php

include_once("../../rotas.php"); // Inclui o arquivo de rotas
include_once($connRoute); // Inclui o arquivo de conexao

if (isset($_SESSION['tipo'])) {
    // $_SESSION['msgRotaProibidaCli'] = "Você Não possui permissão para entrar nessa página";
    header("Location: " . $homeRoute);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Corporativo - Hamtaro PetShop</title>
    <link rel="stylesheet" href="../css-estatico/header.css">
    <link rel="stylesheet" href="../css-dinamico/login-cliente.css">
    <link rel="stylesheet" href="../css-dinamico/login-funcionario.css">
</head>

<body>

    <header>
        <a href="<?php echo $homeRoute; ?>" id="logo">
            <p>Hamtaro Petshop</p>
        </a>
        <div class="links-login-cliente">
            <a href="<?php echo $loginCliRoute; ?>" class="corporativo">Cliente</a>
            <div class="acesso-seguro">
                <p>Ambiente Seguro</p>
                <img src="../img-dinamico/cadeado.svg" alt="">
            </div>
        </div>
    </header>

    <div class="container box-conteudo">

        <form action="<?php echo $procLoginFunRoute; ?>" method="post">

            <div class="titulos">
                <h1>Acesse sua conta corporativa</h1>
                <p>Informe seu cpf e senha de cadastro</p>
            </div>



            <div class="inputs">

                <div class="login">
                    <label for="login">CPF</label><br>
                    <input type="text" name="login" pattern="\d{3}[.]?\d{3}[.]?\d{3}[-]?\d{2}" required>
                </div>

                <div class="senha">
                    <label for="senha">Senha</label><br>
                    <input type="password" name="senha" required>
                </div>

            </div>

            <div class="botoes">

                <input type="submit" value="Entrar">


                <?php
                if (isset($_SESSION['msgloginFun'])) {
                    echo "<p>" . $_SESSION['msgloginFun'] . "</p>";
                    unset($_SESSION['msgloginFun']);
                }
                ?>


                <a class="voltar" href="<?php echo $homeRoute; ?>">Voltar</a>

            </div>
        </form>
    </div>


    <div class="box-cadastro">
        <span>Ainda não possui cadastro corporativo? <a href=""> Informe seu gestor.</a></span>
    </div>
</body>

</html>
