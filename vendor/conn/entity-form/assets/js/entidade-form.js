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

    $scope.attrValid = function () {
        return (!(($scope.attr.type === 'list' || $scope.attr.type === 'extend' || $scope.attr.type === 'extendMult' || $scope.attr.type === 'listMult') && !$scope.attr.table) && $scope.attr.type && $scope.attr.title);
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

    /**
     * =======================
     *      VARIBLES
     * =======================
     * */

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
        'on': 'On',
        'date': 'Data',
        'datetime': 'Data & Hora',
        'time': 'Hora',
        'week': 'Semana',
        'extend': 'Extensão',
        'extendMult': 'Extensão Multipla',
        'list': 'Lista de Seleção',
        'listMult': 'Lista Multipla',
        'text': 'Texto',
        'textarea': 'Área de Texto',
        'int': 'Inteiro',
        'valor': 'R$ Valor',
        'email': 'Email',
        'cpf': 'Cpf',
        'cnpj': 'Cnpj',
        'password': 'Password',
        'select': 'Select'
    };

    /**
     * =======================
     *      FUNCTIONS
     * =======================
     * */

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
        attrCriadas = [];
        attrModificadas = [];
        attrDeletadas = [];

        if (id !== "") {
            $.post(HOME + 'request/post', {file: 'readEntity', lib: 'entity-form', entidade: id}, function (g) {
                $scope.listAttr = [];
                var dados = fixValuesAttr($.parseJSON(g));
                $.each(dados, function (i, dado) {
                    $scope.listAttr.push(dado);
                });
                $scope.addNewAtributo();
                setTimeout(function () {
                    $scope.$apply();
                }, 1);
            });
        } else {
            $("#entity").focus();
            $scope.listAttr = [];
            $scope.addNewAtributo();
            setTimeout(function () {
                $scope.$apply();
            }, 1);
        }
    };

    $scope.editAttr = function (attr) {
        if ($scope.attrValid()) {
            $scope.addAttr();
        }
        $scope.addNewAtributo(attr);
    };

    //duplicidade de nomes de atributos
    //modificações na tabela não muito precisas
    //algumas incompatibilidades entre a entity e a entity-form no quesito defaults

    $scope.addAttr = function () {
        var update = contain($scope.attr, $scope.listAttr);

        if ($scope.attrValid()) {
            if (update === -1) {
                attrCriadas.push($scope.attr.column);
                $scope.listAttr.push($scope.attr);
            } else {
                var mod = contain($scope.attr.column, attrModificadas);
                var del = contain($scope.attr.column, attrDeletadas);
                var add = contain($scope.attr.column, $scope.listAttr);
                if (mod === -1 && del === -1 && add === -1) {
                    attrModificadas.push($scope.attr.column);

                } else if ((del > -1 || add > -1) && mod > -1) {
                    attrModificadas.splice(mod, 1);
                }
            }
            $scope.addNewAtributo();
        }
    };

    $scope.createEntity = function () {
        if ($scope.entity.title && $scope.listAttr.length > 1) {
            $scope.addAttr();
            checkIdFirst();

            console.log(attrCriadas);
            console.log(attrModificadas);
            console.log(attrDeletadas);

            $.post(HOME + "request/post", {
                lib: "entity-form",
                file: "createEntity",
                dados: $scope.listAttr,
                entity: $scope.entity.slug,
                add: attrCriadas,
                mod: attrModificadas,
                del: attrDeletadas
            }, function (g) {
                if (g) {
                    Materialize.toast("Erro ao Salvar Entidade", 3000);
                    console.log(g);
                } else {
                    $scope.readEntity();
                    Materialize.toast('Entidade Salva!', 2500);
                }
                attrCriadas = [];
                attrModificadas = [];
                attrDeletadas = [];
            });
        }
    };

    $scope.removeEntity = function () {
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
    };

    $scope.deleteAttr = function () {
        var indice = $scope.listAttr.indexOf($scope.attr);
        if (indice > -1) {
            $scope.listAttr.splice(indice, 1);

            var mod = contain($scope.attr.column, attrModificadas);
            if (mod > -1) {
                attrModificadas.splice(mod, 1);
            }
            var add = contain($scope.attr.column, attrCriadas);
            if (add > -1) {
                attrCriadas.splice(add, 1);
            } else {
                if (contain($scope.attr.column, attrDeletadas) === -1) {
                    attrDeletadas.push($scope.attr.column);
                }
            }

            Materialize.toast("Atributo Removido", 2500);
            $scope.addNewAtributo();
        } else {
            $scope.addNewAtributo();
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
        $scope.attr["null"] = "null" in $scope.attr ? $scope.attr['null'] : false;
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
            }, 1);
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
