jQuery.validator.addMethod("cpf_cnpj", function (cpf_cnpj, element) {

    var $return = true;

    cpf_cnpj = cpf_cnpj.replace(/[^\d]+/g, "");

    if (cpf_cnpj.length > 11 && cpf_cnpj.length < 14 || cpf_cnpj.length > 14){
        $return = false;

    }else if (cpf_cnpj.length == 11) {

        var cpf = cpf_cnpj;
        informacao = '';

        // this is mostly not needed
        var invalidos = [
             '11111111111',
             '22222222222',
             '33333333333',
             '44444444444',
             '55555555555',
             '66666666666',
             '77777777777',
             '88888888888',
             '99999999999',
             '00000000000'
        ];
        for (i = 0; i < invalidos.length; i++) {
            if (invalidos[i] == cpf) {
                $return = false;
            }
        }

        //validando primeiro digito
        add = 0;
        for (i = 0; i < 9; i++) {
            add += parseInt(cpf.charAt(i), 10) * (10 - i);
        }
        rev = 11 - (add % 11);
        if (rev == 10 || rev == 11) {
            rev = 0;
        }
        if (rev != parseInt(cpf.charAt(9), 10)) {
            $return = false;
        }

        //validando segundo digito
        add = 0;
        for (i = 0; i < 10; i++) {
            add += parseInt(cpf.charAt(i), 10) * (11 - i);
        }
        rev = 11 - (add % 11);
        if (rev == 10 || rev == 11) {
            rev = 0;
        }
        if (rev != parseInt(cpf.charAt(10), 10)) {
            $return = false;
        }


    } else if (cpf_cnpj.length == 14) {

        var cnpj = cpf_cnpj;

        var numeros, digitos, soma, resultado, pos, tamanho,
            digitos_iguais = true;

        if (cnpj.length < 14 && cnpj.length > 15)
            $return = false;

        for (var i = 0; i < cnpj.length - 1; i++)
            if (cnpj.charAt(i) != cnpj.charAt(i + 1)) {
                digitos_iguais = false;
                break;
            }

        if (!digitos_iguais) {
            tamanho = cnpj.length - 2
            numeros = cnpj.substring(0, tamanho);
            digitos = cnpj.substring(tamanho);
            soma = 0;
            pos = tamanho - 7;

            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2)
                    pos = 9;
            }

            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;

            if (resultado != digitos.charAt(0))
                $return = false;

            tamanho = tamanho + 1;
            numeros = cnpj.substring(0, tamanho);
            soma = 0;
            pos = tamanho - 7;

            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2)
                    pos = 9;
            }

            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;

            if (resultado != digitos.charAt(1))
                $return = false;
        }

        //$return = true;
    }

    return $return;

});