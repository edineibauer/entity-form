var app = angular.module('entity-form', []);
app.controller('entity-controller', function ($scope) {

    /**
     * =======================
     *      VARIBLES
     * =======================
     * */

    $scope.entityList = [];

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

        if(id !== "") {
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
        $scope.addNewAtributo(attr);
    };

    $scope.addAttr = function () {
        var update = contain($scope.attr, $scope.listAttr);

        if(!(($scope.attr.type === 'list' || $scope.attr.type === 'extend' || $scope.attr.type === 'extendMult' || $scope.attr.type === 'listMult') && !$scope.attr.table)) {
            if (update === -1 && $scope.attr.type && $scope.attr.title) {
                $scope.listAttr.push($scope.attr);
            }
            $scope.addNewAtributo();
        }
    };

    $scope.createEntity = function () {

        if ($scope.entity.title && $scope.listAttr.length > 1) {
            $.post(HOME + "request/post", {
                lib: "entity-form",
                file: "createEntity",
                dados: $scope.listAttr,
                entity: $scope.entity.slug
            }, function (g) {
                if (g) {
                    Materialize.toast("Erro ao Salvar Entidade", 3000);
                    console.log(g);
                } else {
                    $scope.readEntity();
                    Materialize.toast('Entidade Salva!', 2500);
                }
            });
        }
    };

    $scope.deleteAttr = function () {
        var indice = $scope.listAttr.indexOf($scope.attr);
        if(indice > -1) {
            delete $scope.listAttr.splice(indice, 1);

            setTimeout(function () {
                Materialize.toast("Atributo Removido", 2500);
                $scope.$apply();
            }, 1);
        }
    }

    function fixValuesAttr(dados) {
        $.each(dados, function (index, dado) {
            dados[index]['title'] = index;
            dados[index]['slug'] = index;
        });

        return dados;
    }

    $scope.addNewAtributo = function (attr) {
        $scope.attr = (typeof(attr) === 'undefined' ? {} : attr);
        $scope.attr["title"] = "title" in $scope.attr ? $scope.attr['title'] : "";
        $scope.attr["slug"] = "slug" in $scope.attr ? $scope.attr['slug'] : "";
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
            $scope.attr.slug = slug(newNames, "_");
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
});
