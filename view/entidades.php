<div class="row" ng-app="entity-form" ng-controller="entity-controller" style="margin-bottom: 0;">
    <ul id="nav-mobile" class="side-nav fixed blue white-text z-depth-2"
        style="padding:5px 0 5px 5px; transform: translateX(0%); overflow: visible;">
        <ul class="collection" style="border:none; position: relative; overflow: visible">

            <li class='collection-item' style="background: transparent; border:none">
                <div class="row right-align">
                    <a class="waves-effect waves-teal btn blue lighten-1" id="newEntityBtn" ng-click="editEntity()"><i
                                class="material-icons white-text">add</i></a>
                </div>
            </li>

            <li class='collection-item' ng-repeat="enty in entityList" style="background: transparent; border:none">
                <div class="col s7" style="padding: 7px 0;">{{enty}}</div>

                <div class="col s5 right-align">
                    <a class="waves-effect waves-teal btn-flat" ng-click="editEntity(enty)">
                        <i class="material-icons white-text">edit</i>
                    </a>
                </div>
            </li>
        </ul>

    </ul>
    <div id="main" class="row" style="margin-bottom: 0;" ng-cloak>

            <form class="col s12 m4">
                <div class="clearfix">
                    <br>
                </div>
                <div class="row">
                    <div class="row">
                        <div class="col s12 m6">
                            <button class="btn waves-effect waves-light blue" type="submit" id="createEntity"
                                    name="action"
                                    ng-click="createEntity()">Salvar
                                <i class="material-icons right">check</i>
                            </button>
                        </div>
                        <div class="col s12 m6 right-align" ng-show="entity.slug != '' && listAttr[1]">
                            <button class="btn-flat waves-effect waves-light grey lighten-3 grey-text" name="action"
                            ng-click="removeEntity()">
                                <i class="material-icons right">delete</i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="entity" type="text" class="validate" ng-required="true" ng-model="entity.title">
                            <label for="entity">Nome da Entidade</label>
                        </div>
                    </div>
                    <ul class="collection">
                        <li class="collection-item" ng-repeat="at in listAttr">{{at.column}}

                            <a class="waves-effect waves-red btn-flat" ng-click="editAttr(at)"
                               style="float: right; margin-top: -7px; margin-right: -15px; padding-left: 15px;">
                                <i class="material-icons right">edit</i>


                            <a ng-click="downAttr(at)" class="waves-effect waves-teal btn-flat" style="padding: 0;float: right; margin-top: -7px;"><i class="material-icons right" style="margin: 0">arrow_downward</i></a>
                            <a ng-click="upAttr(at)" class="waves-effect waves-teal btn-flat" style="padding: 0;float: right; margin-top: -7px;"><i class="material-icons right" style="margin: 0">arrow_upward</i></a>

                        </li>
                    </ul>
                </div>
            </form>

            <form class="col s12 m8 white" name="attrForm" id="attrForm" ng-model="attrForm"
                  style="padding-left: 40px;">
                <div class="clearfix">
                    <br>
                </div>
                <div class="row">
                    <div class="col s12 m6 left-align" id="novoString" ng-show="!attr.type">
                        <button class="btn-flat">
                            Novo Atributo
                        </button>
                    </div>
                    <div class="col s12 m6 left-align" ng-show="attr.type">
                        <button class="btn waves-effect waves-light" type="submit" name="action" ng-click="addAttr()">
                            adicionar
                            <i class="material-icons right">add</i>
                        </button>
                    </div>
                    <div class="col s12 m6 right-align" ng-show="attrFullFieldsRequireds()">
                        <button class="btn waves-effect waves-light grey lighten-3 grey-text"
                                name="action" ng-click="deleteAttr()">
                            Remover
                            <i class="material-icons right">delete</i>
                        </button>
                    </div>
                </div>
                <div class="clearfix" style="height: 13px">
                </div>

                <div class="row">
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <select ng-model="attr.type" ng-disabled="attr.$$hashKey" id="selectType" ng-required="true">
                                <option value="" disabled selected>Selecione</option>
                                <option ng-repeat="(k, v) in dataList" value="{{k}}">{{v}}</option>
                            </select>
                            <label>Função</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="nome" type="text" class="validate" ng-required="true" ng-model="attr.title">
                            <label for="nome">Nome do Atributo</label>
                        </div>

                    </div>

                    <div class="col s12 m6">
                        <div class="switch col s12">
                            <input type="checkbox" ng-disabled="attr.unique || attr.type == 'pri'" id="null" ng-model="attr.null"/>
                            <label for="null">Nulo</label>
                        </div>
                        <div class="switch col s12">
                            <input type="checkbox" ng-disabled="attr.type == 'pri'" id="unique" ng-model="attr.unique"/>
                            <label for="unique">Unico</label>
                        </div>
                        <div class="switch col s12">
                            <input type="checkbox" ng-disabled="attr.type == 'pri'" id="indice" ng-model="attr.indice"/>
                            <label for="indice">Indice</label>
                        </div>
                        <div class="switch col s12">
                            <input type="checkbox" ng-disabled="attr.type == 'pri'" id="update" ng-model="attr.update"/>
                            <label for="update">Permitir Atualização</label>
                        </div>

                    </div>

                    <div class="col s12 m6">
                        <div class="input-field col s12"
                             ng-show="attr.type == 'list' || attr.type == 'extend' || attr.type == 'extend_mult' || attr.type == 'list_mult'">
                            <select ng-model="attr.table" ng-disabled="attr.$$hashKey"
                                    ng-required="attr.type == 'list' || attr.type == 'extend' || attr.type == 'extend_mult' || attr.type == 'list_mult'">
                                <option value="" disabled selected>Selecione um</option>
                                <option ng-repeat="entSelect in entityList">{{entSelect}}</option>
                            </select>
                            <label>Entidade</label>
                        </div>
                        <div class="col s12"
                             ng-show="attr.type == 'text' || attr.type == 'textarea' || attr.type == 'int' || attr.type == 'title'">
                            <div class="input-field col s12">
                                <input id="size" type="number" class="validate"
                                       ng-model="attr.size">
                                <label for="size">Tamanho</label>
                            </div>
                        </div>

                        <div class="col s12" ng-if="attr.null">
                            <div class="input-field col s12">
                                <input id="default" type="text" class="validate"
                                       ng-model="attr.default">
                                <label for="default">Valor Padrão</label>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix" style="height: 13px">
                    </div>

                    <div class="row">
                        <ul class="collapsible" data-collapsible="accordion">
                            <li>
                                <div class="collapsible-header active"><i class="material-icons">call_to_action</i>Input
                                    & Table
                                </div>
                                <div class="collapsible-body">

                                    <div class="switch col s12 m6">
                                        <input type="checkbox" id="edit" ng-disabled="attr.type == 'pri'" ng-model="attr.edit"/>
                                        <label for="edit">Mostrar ao Editar</label>
                                    </div>
                                    <div class="switch col s12 m6">
                                        <input type="checkbox" id="list" ng-model="attr.list"/>
                                        <label for="list">Mostrar em Tabelas de Listagem</label>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">filter_drama</i>CSS &
                                    Style
                                </div>
                                <div class="collapsible-body">

                                    <div class="col s12">
                                        <div class="input-field col s12">
                                            <input id="class" type="text" class="validate"
                                                   ng-model="attr.class">
                                            <label for="class">Class</label>
                                        </div>
                                    </div>
                                    <div class="col s12">
                                        <div class="input-field col s12">
                                            <input id="style" type="text" class="validate"
                                                   ng-model="attr.style">
                                            <label for="style">Style</label>
                                        </div>
                                    </div>
                                    <div class="clearfix" style="height: 13px">
                                    </div>

                                    <div class="col s12">
                                        <div class="input-field col s12">
                                            <select ng-model="attr.col" id="selectCol">
                                                <option value="" selected>container</option>
                                                <option value="2">metade</option>
                                                <option value="3">1 Terço</option>
                                                <option value="4">1 Quarto</option>
                                                <option value="5">1/5</option>
                                                <option value="6">1/6</option>
                                            </select>
                                            <label>Colunas</label>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">check</i>Validação</div>
                                <div class="collapsible-body">
                                    <div class="clearfix"></div>

                                    <div class="col s12">
                                        <div class="input-field col s12 m4">
                                            <input id="allow" placeholder="separe por virgula" type="text"
                                                   class="validate"
                                                   ng-model="attr.allow">
                                            <label for="allow">Valores Permitidos</label>
                                        </div>

                                        <div class="input-field col s12 m8" ng-if="attr.allow">
                                            <input id="allowRelated" type="text"
                                                   class="validate" ng-model="attr.allowRelation">
                                            <label for="allowRelated">De nome aos valores Permitidos</label>
                                        </div>
                                    </div>
                                    <div class="col s12">
                                        <div class="input-field col s12">
                                            <input id="regular" type="text" class="validate"
                                                   ng-model="attr.regular">
                                            <label for="allow">Expressão Regular para Validação</label>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                </div>
                            </li>
                            <li>
                                <div class="collapsible-header"><i class="material-icons">whatshot</i>Mineração
                                </div>
                                <div class="collapsible-body">
                                    <div class="clearfix"></div>

                                    <div class="col s12 m6">
                                        <div class="input-field col s12">
                                            <input id="pref" placeholder="separe com vírgula" type="text" class="validate" ng-model="attr.prefixo">
                                            <label for="pref">Prefixo</label>
                                        </div>
                                    </div>
                                    <div class="col s12 m6">
                                        <div class="input-field col s12">
                                            <input id="sulf" placeholder="separe com vírgula" type="text" class="validate" ng-model="attr.sulfixo">
                                            <label for="sulf">Sulfixo</label>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
    </div>
</div>
