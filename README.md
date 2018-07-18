# Informações

Tomei a liberdade de usar uma ferramenta de terminal que uso fiz há alguns anos para Scripts.

Segue o link : [PHPTTOOLS](http://asd.com)



# Uso:

  
```bash
$ ./phpttools --help
```

  Lista de procedimentos :

    quadrado :
      Calcula quadrado perfeito

        --file='string'
          Caminho do arquivo

    produtos :
      Produtos

        --file='string'
          Caminho do arquivo json de produtos

---

# Quadrado Perfeito

O Script está em [./ScriptTools/Quadrado.script.php](https://github.com/fernandohcorrea/amaro/blob/master/ScriptTools/Quadrado.script.php)

Usei o menor número de Iterações possível.

```bash
$ ./phpttools quadrado --file=./3p3.txt
```

```bash
$ ./phpttools quadrado --file=./4p4.txt
```

```bash
$ ./phpttools quadrado --file=./17p17.txt
```

---

# Produtos

O Script está em [./ScriptTools/Produtos.script.php](https://github.com/fernandohcorrea/amaro/blob/master/ScriptTools/Produtos.script.php)

Usei um terminal com prompt.

Você deve digitar o ID para fazer a busca.

Ex: 8363

```bash
$ ./phpttools produtos --file=produtos.json
```