>LINK PARA O CÓDIGO DA TELA DE LOGIN<
https://dontpad.com/grao_nobre_web2

>LINK PARA O CÓDIGO DO DIAGRAMA DE MODELAGEM DE DADOS DO SISTEMA PROJETADO<
https://dontpad.com/grao_nobre_web2/sql_code

>CÓDIGO SQL DO BANCO DE DADOS<
CREATE DATABASE sistema_pedidos;
USE sistema_pedidos;


CREATE TABLE Cliente (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    endereco_entrega VARCHAR(255)
);


CREATE TABLE Funcionario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cargo VARCHAR(50),
    login VARCHAR(50) UNIQUE NOT NULL,
    senha VARCHAR(100) NOT NULL
);


CREATE TABLE Produto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(50),
    descricao TEXT,
    imagem VARCHAR(255),
    disponibilidade BOOLEAN DEFAULT TRUE,
    id_funcionario INT,
    FOREIGN KEY (id_funcionario) REFERENCES Funcionario(id)
);


CREATE TABLE Estoque (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_insumo VARCHAR(100) NOT NULL,
    quantidade INT NOT NULL,
    validade DATE,
    idProduto INT,
    FOREIGN KEY (idProduto) REFERENCES Produto(id)
);


CREATE TABLE Pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    status VARCHAR(50),
    total DECIMAL(10,2),
    forma_pagamento VARCHAR(50),
    id_cliente INT,
    id_funcionario INT,
    FOREIGN KEY (id_cliente) REFERENCES Cliente(id),
    FOREIGN KEY (id_funcionario) REFERENCES Funcionario(id)
);


CREATE TABLE ItemPedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    id_pedido INT,
    id_produto INT,
    FOREIGN KEY (id_pedido) REFERENCES Pedido(id),
    FOREIGN KEY (id_produto) REFERENCES Produto(id)
);
