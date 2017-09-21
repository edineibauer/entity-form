var app = angular.module('entity-form', []);
app.controller('entity-controller', function ($scope) {

    Array.prototype.move = function (old_index, new_index) {
        if (new_index >= this.length) {
            var k = new_index - this.length;
            while ((k--) + 1) {
                this.push(undefined);
            }
        }
        this.splice(new_index, 0, this.splice(old_index, 1)[0]);
        return this; // for testing purposes
    };

    $scope.attrFullFieldsRequireds = function () {
        return (!(($scope.attr.type === 'list' || $scope.attr.type === 'extend' || $scope.attr.type === 'extend_mult' || $scope.attr.type === 'list_mult') && !$scope.attr.table) && $scope.attr.type && $scope.attr.title);
    };

    /**
     * =======================
     *      VARIBLES
     * =======================
     * */

    var editando = "";
    var identificador = 0;
    $scope.entityList = [];
    var attrCriadas = [];
    var attrDeletadas = [];
    var attrModificadas = [];

    $scope.dataList = {
        'pri': 'Chave Primária',
        'title': 'Title',
        'link': 'Link',
        'cover': 'Cover',
        'status': 'Status',
        'date': 'Data',
        'extend': 'Extensão',
        'extend_mult': 'Extensão Multipla',
        'list': 'Lista',
        'list_mult': 'Lista Multipla',
        'text': 'Texto',
        'textarea': 'Área de Texto',
        'int': 'Inteiro',
        'float': 'Float',
        'datetime': 'Data & Hora',
        'time': 'Hora',
        'week': 'Semana',
        'on': 'On',
        'valor': 'R$ Valor',
        'select': 'Select',
        'email': 'Email',
        'cpf': 'Cpf',
        'cnpj': 'Cnpj',
        'password': 'Password'
    };

    /**
     * =======================
     *      FUNCTIONS
     * =======================
     * */

    function notExistSameNome() {
        var result = true;
        $.each($scope.listAttr, function (i, attr) {
            $.each($scope.listAttr, function (e, attr2) {
                if (i !== e && attr.column === attr2.column) {
                    result = false;
                    return false;
                }
            });
        });

        return result;
    }

    function checkIdFirst() {
        var haveId = false;
        $.each($scope.listAttr, function (i, dado) {
            if (dado.type === 'pri') {
                haveId = true;
                if (i > 0) {
                    for (var e = i; e > 0; e--) {
                        $scope.upAttr(dado);
                    }

                    Materialize.toast("Chave Primária movida para inicio", 3000);
                }
            }
        });

        if (!haveId) {
            Materialize.toast("entidade sem Chave Primária não pode ser Editada!", 5000);
        }
    }

    function fixValuesAttr(dados) {
        $.each(dados, function (index, dado) {
            dados[index]['title'] = index;
            dados[index]['column'] = index;
        });

        return dados;
    }

    $scope.readEntity = function () {
        $.post(HOME + 'request/post', {file: 'readEntitys', lib: 'entity-form'}, function (g) {
            $scope.entityList = $.parseJSON(g);
            setTimeout(function () {
                $scope.$apply();
            }, 1);
        });
    };

    $scope.editEntity = function (id) {
        var id = typeof(id) === "undefined" ? "" : id;
        $scope.entity.title = id;
        $scope.entity.slug = id;
        identificador = 0;
        attrCriadas = [];
        attrModificadas = [];
        attrDeletadas = [];

        if (id !== "") {
            editando = id;
            $.post(HOME + 'request/post', {file: 'readEntity', lib: 'entity-form', entidade: id}, function (g) {
                $scope.listAttr = [];
                var dados = fixValuesAttr($.parseJSON(g));
                $.each(dados, function (i, dado) {
                    identificador = identificador < dado.identificador ? dado.identificador : identificador;
                    $scope.listAttr.push(dado);
                });
                identificador++;
                $scope.addNewAtributo();
                setTimeout(function () {
                    $scope.$apply();
                }, 1);
            });
        } else {
            editando = "";
            $("#entity").focus();
            $scope.listAttr = [];
            $scope.addNewAtributo();
            setTimeout(function () {
                $scope.$apply();
            }, 1);
        }
    };

    $scope.editAttr = function (attr) {
        if ($scope.attrFullFieldsRequireds()) {
            $scope.addAttr();
        }
        $scope.addNewAtributo(attr);
    };

    $scope.addAttr = function () {
        if ($scope.attrFullFieldsRequireds()) {
            if (typeof($scope.attr.identificador) === 'undefined') {

                $scope.attr.identificador = identificador;
                identificador++;
                $scope.listAttr.push($scope.attr);
                attrCriadas.push($scope.attr.identificador);

            } else {

                var notInMod = contain($scope.attr.identificador, attrModificadas) === -1;
                var notInDel = contain($scope.attr.identificador, attrDeletadas) === -1;
                var notInAdd = contain($scope.attr.identificador, attrCriadas) === -1;
                if (notInMod && notInDel && notInAdd) {
                    attrModificadas.push($scope.attr.identificador);
                }
            }

            $scope.addNewAtributo();
        }
    };

    $scope.createEntity = function () {
        if ($scope.entity.title && $scope.listAttr.length > 1) {
            $scope.addAttr();
            if (notExistSameNome()) {
                checkIdFirst();

                $.post(HOME + "request/post", {
                    lib: "entity-form",
                    file: "createEntity",
                    dados: $scope.listAttr,
                    entity: $scope.entity.slug,
                    edit: editando,
                    add: attrCriadas,
                    mod: attrModificadas,
                    del: attrDeletadas
                }, function (g) {
                    if (g) {
                        Materialize.toast(g, 3000);
                    } else {
                        $scope.readEntity();
                        Materialize.toast('Entidade Salva!', 2500);
                        attrCriadas = [];
                        attrModificadas = [];
                        attrDeletadas = [];
                    }
                });
            } else {
                Materialize.toast("Nomes de atributos repetidos!", 3000);
            }
        }
    };

    $scope.removeEntity = function () {
        if (confirm("Deletar entidade e dados?")) {
            if ($scope.entity.slug && $scope.listAttr.length > 1) {
                $.post(HOME + "request/post", {
                    lib: "entity-form",
                    file: "deleteEntity",
                    entity: $scope.entity.slug
                }, function (g) {
                    if (g) {
                        Materialize.toast(g, 3000);
                    } else {
                        $scope.readEntity();
                        $scope.editEntity();
                        $scope.addNewAtributo();
                        Materialize.toast('Entidade Removida!', 2500);
                    }
                });
            }
        }
    };

    $scope.deleteAttr = function () {
        if (confirm("Remover atributo?")) {
            var indice = $scope.listAttr.indexOf($scope.attr);

            if (indice > -1) {
                $scope.listAttr.splice(indice, 1);

                var add = contain($scope.attr.identificador, attrCriadas);
                if (add > -1) {
                    attrCriadas.splice(add, 1);
                } else {
                    var mod = contain($scope.attr.identificador, attrModificadas);
                    if (mod > -1) {
                        attrModificadas.splice(mod, 1);
                    }
                    attrDeletadas.push($scope.attr.identificador);
                }

                Materialize.toast("Atributo Removido", 2500);
                $scope.addNewAtributo();
            }
        }
    };

    $scope.downAttr = function (id) {
        var oldPosition = $scope.listAttr.indexOf(id);
        var newPosition = oldPosition + (oldPosition < $scope.listAttr.length ? 1 : 0);
        $scope.listAttr.move(oldPosition, newPosition);
    };

    $scope.upAttr = function (id) {
        var oldPosition = $scope.listAttr.indexOf(id);
        var newPosition = oldPosition - (oldPosition > 0 ? 1 : 0);
        $scope.listAttr.move(oldPosition, newPosition);
    };

    $scope.addNewAtributo = function (attr) {
        $scope.attr = (typeof(attr) === 'undefined' ? {} : attr);
        $scope.attr["title"] = "title" in $scope.attr ? $scope.attr['title'] : "";
        $scope.attr["column"] = "column" in $scope.attr ? $scope.attr['column'] : "";
        $scope.attr["type"] = "type" in $scope.attr ? $scope.attr['type'] : "";
        $scope.attr["size"] = "size" in $scope.attr ? $scope.attr['size'] : "";
        $scope.attr["allow"] = "allow" in $scope.attr ? $scope.attr['allow'] : "";
        $scope.attr["allowRelation"] = "allowRelation" in $scope.attr ? $scope.attr['allowRelation'] : "";
        $scope.attr["default"] = "default" in $scope.attr ? $scope.attr['default'] : "";
        $scope.attr["null"] = "null" in $scope.attr ? $scope.attr['null'] : true;
        $scope.attr["unique"] = "unique" in $scope.attr ? $scope.attr['unique'] : false;
        $scope.attr["indice"] = "indice" in $scope.attr ? $scope.attr['indice'] : false;
        $scope.attr["update"] = "update" in $scope.attr ? $scope.attr['update'] : true;
        $scope.attr["edit"] = "edit" in $scope.attr ? $scope.attr['edit'] : true;
        $scope.attr["list"] = "list" in $scope.attr ? $scope.attr['list'] : true;
        $scope.attr["table"] = "table" in $scope.attr ? $scope.attr['table'] : "";
        $scope.attr["col"] = "col" in $scope.attr ? $scope.attr['col'] : "";
        $scope.attr["class"] = "class" in $scope.attr ? $scope.attr['class'] : "";
        $scope.attr["style"] = "style" in $scope.attr ? $scope.attr['style'] : "";
        $scope.attr["regular"] = "regular" in $scope.attr ? $scope.attr['regular'] : "";
        $scope.attr["prefixo"] = "prefixo" in $scope.attr ? $scope.attr['prefixo'] : "";
        $scope.attr["sulfixo"] = "sulfixo" in $scope.attr ? $scope.attr['sulfixo'] : "";

        setTimeout(function () {
            $('select').material_select();
            $scope.$apply();
        }, 1);
    };

    $scope.defaultValues = function () {
        $scope.entity = {
            "title": "",
            "slug": ""
        };
        $scope.listAttr = [];

        setTimeout(function () {
            $scope.$apply();
        }, 1);
    };

    /**
     * =======================
     *      WATCHS
     * =======================
     * */
    $scope.$watch('attr.title', function (newNames, oldNames) {
        if (typeof(newNames) !== 'undefined') {
            $scope.attr.column = slug(newNames, "_");
            setTimeout(function () {
                Materialize.updateTextFields();
            }, 1);
        }
    });

    $scope.$watch('entity.title', function (newNames, oldNames) {
        if (typeof(newNames) !== 'undefined') {
            $scope.entity.slug = slug(newNames, "_");
            setTimeout(function () {
                Materialize.updateTextFields();
            }, 1);
        }
    });

    $scope.$watch('attr.type', function (newValue, oldNames) {
        if (newValue) {
            $scope.attr = funcaoValuesEntity(newValue, $scope.attr);
            setTimeout(function () {
                Materialize.updateTextFields();
                $("#nome").focus();
            }, 1);
        }
    });

    $scope.$watch('attr.unique', function (newValue, oldNames) {
        if (newValue) {
            $scope.attr.null = false;
        }
    });

    /**
     * =======================
     *      CALLS
     * =======================
     * */

    $scope.addNewAtributo();
    $scope.defaultValues();
    $scope.readEntity();
    $scope.editEntity();

});

$(function () {
    $(document).ready(function () {
        $('select').material_select();
    });
    $('input.autocomplete').autocomplete({
        data: {
            'tinyint': null,
            'smallint': null,
            'mediumint': null,
            'int': null,
            'bigint': null,
            'float': null,
            'double': null,
            'date': null,
            'datetime': null,
            'time': null,
            'year': null,
            'char': null,
            'varchar': null,
            'tinytext': null,
            'text': null,
            'mediumtext': null,
            'longtext': null
        },
        limit: 20,
        onAutocomplete: function (val) {
        },
        minLength: 1
    });

    setTimeout(function () {
        $("#newEntityBtn").trigger("click");
    }, 100);
});
