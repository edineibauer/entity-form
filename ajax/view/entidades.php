<?php
if(empty($_SESSION['userlogin']) || $_SESSION['userlogin']['setor'] !== "1" || $_SESSION['userlogin']['nivel'] !== "1") {
    $data['response'] = 3;
    $data['data'] = HOME . "login";
} else {
    ob_start();
    ?>
    <ul id="nav-entity" class="color-text-white z-depth-4 space-header theme-l1">
        <div class="row">
            <div class="panel">
                <div class="col s7 upper padding-medium">
                    Entidades
                </div>
                <div class="col s5 align-right">
                    <button class="btn color-white btn-floating right" id="newEntityBtn" onclick="entityEdit()">
                        <i class="material-icons">add</i>
                    </button>
                </div>
            </div>
        </div>
        <ul class="row">
            <li class="col s12" id="entity-space">
            </li>
            <div class="col s12 hide" id="tpl-entity">
                <div class="col s7 padding-small">__$__</div>
                <div class="padding-small col s5 align-right">
                    <button class="color-text-white radius pointer right padding-tiny btn-flat" onclick="removeEntity('__$__')">
                        <i class="material-icons white-text font-medium">delete</i>
                    </button>
                    <button class="color-text-white radius pointer right padding-tiny btn-flat" style="margin-right: 5px;"
                       onclick="entityEdit('__$__')">
                        <i class="material-icons white-text font-medium">edit</i>
                    </button>
                </div>
            </div>
        </ul>
    </ul>

    <form class="col s12 m4 z-depth-2 space-header color-white" id="nav-menu">
        <header class="row">
            <div class="panel">
                <div class="col s12 padding-tiny">
                    <button class="btn color-blue left" type="submit" id="saveEntityBtn" name="action"
                            onclick="saveEntity()">
                        salvar
                        <i class="material-icons right padding-left">check</i>
                    </button>
                    <button class="btn color-grey right" type="submit" title="Novo Atributo" id="saveAttrBtn"
                            name="action"
                            onclick="editAttr()">
                        <i class="material-icons right">add</i>
                    </button>
                </div>
            </div>
        </header>
        <div class="row"></div>
        <div class="panel" id="space-attr-entity">
            <div class="row">
                <label class="col s12">
                    <span>Nome da Entidade</span>
                    <input id="entityName" type="text" placeholder="entidade..." class="font-large">
                </label>
            </div>
            <div class="col s12"><br></div>

            <div class="row hide" id="importForm">
                <hr>
                <br>
                <span class="row">
                <label for="import">Importar Entidade</label>
                <input type="file" name="import" id="import"/>
            </span>
                <button class="col s12 btn-large color-hover-cyan" onclick="sendImport()">
                    <i class="material-icons padding-right left">send</i><span class="left">Enviar</span>
                </button>
            </div>

            <ul class="row" id="entityAttr"></ul>

            <li class="col s12 hide" id="tpl-attrEntity" style="border-bottom: solid 1px #EEE;">
                __$1__
                <button class=" btn-flat pointer right color-text-white radius" onclick="deleteAttr(__$0__)"><i
                            class="material-icons right font-large">delete</i></button>
                <button class="waves-effect waves-red btn-flat pointer right" style="margin-right: 10px;"
                   onclick="editAttr(__$0__)">
                    <i class="material-icons right">edit</i></button>
            </li>
        </div>
    </form>

    <div id="main" class="row color-gray-light space-header">
        <div class="col s12 hide" id="requireNameEntity">
            <div class="card padding-medium">
                <div class="row">
                    <div class="col s12 m4 padding-small pad">
                        <label class="row" for="funcaoPrimary">Genérico</label>
                        <select class="selectInput" id="funcaoPrimary">
                            <option value="" disabled selected>Input Genérica</option>
                            <option value="text">Texto</option>
                            <option value="textarea">Área de Texto</option>
                            <option value="html">Área de HTML</option>
                            <option value="int">Inteiro</option>
                            <option value="float">Float</option>
                            <option value="boolean">Boleano</option>
                            <option value="select">Select</option>
                            <option value="radio">Radio</option>
                            <option value="checkbox">CheckBox</option>
                            <!--                        <option value="range">Range</option>-->
                            <option value="color">Cor</option>
                            <option value="source">Arquivo</option>
                            <option value="sources">Arquivos Multiplos</option>
                            <option value="string">Informação</option>
                        </select>
                    </div>
                    <div class="col s12 m4 padding-small">
                        <label class="row" for="funcaoIdentifier">Semântico</label>
                        <select class="selectInput" id="funcaoIdentifier">
                            <option value="" disabled selected>Input de Identidade</option>
                            <option value="title">Título</option>
                            <option value="link">Link</option>
                            <option value="status">Status</option>
                            <option value="valor">R$ Valor</option>
                            <option value="url">Url</option>
                            <option value="email">Email</option>
                            <option value="password">Password</option>
                            <option value="tel">Telefone</option>
                            <option value="cpf">Cpf</option>
                            <option value="cnpj">Cnpj</option>
                            <option value="ie">Inscrição Estadual</option>
                            <option value="rg">RG</option>
                            <option value="cep">Cep</option>
                            <option value="date">Data</option>
                            <option value="datetime">Data & Hora</option>
                            <option value="time">Hora</option>
                            <option value="week">Semana</option>
                            <option value="month">Mês</option>
                            <option value="year">Ano</option>
                        </select>
                    </div>
                    <div class="col s12 m4 padding-small">
                        <label class="row" for="funcaoRelation">Relacional</label>
                        <select class="selectInput" id="funcaoRelation">
                            <option value="" disabled selected>Input Relacional</option>
                            <option value="extend">Extensão</option>
                            <option value="extend_mult">Extensão Multipla</option>
                            <option value="list">Lista</option>
                            <option value="list_mult">Lista Multipla</option>
                            <option value="selecao">Seleção</option>
                            <option value="selecao_mult">Seleção Multipla</option>
                            <option value="publisher">Autor</option>
                        </select>
                    </div>
                </div>

                <div class="col s12">
                    <div class="col s12 m8 l8 padding-small hide" id="nomeAttr">
                        <label for="nome">Nome do Atributo</label>
                        <input id="nome" autocomplete="off" type="text" class="input">
                    </div>

                    <div class="col s12 m4 l4 hide" id="relation_container">
                        <label>Entidade Relacionada</label>
                        <select class="input" id="relation"></select>
                    </div>

                    <div class="row requireName hide">

                        <div class="col s6 m3 l1">
                            <label class="row" for="update">Update</label>
                            <label class="switch">
                                <input type="checkbox" class="input" id="update">
                                <div class="slider"></div>
                            </label>
                        </div>

                        <div class="col s6 m3 l1">
                            <label class="row" for="unique">Único</label>
                            <label class="switch">
                                <input type="checkbox" class="input" id="unique">
                                <div class="slider"></div>
                            </label>
                        </div>

                        <div class="col s6 m3 l1">
                            <label class="row" for="default_custom">Nulo</label>
                            <label class="switch">
                                <input type="checkbox" id="default_custom">
                                <div class="slider"></div>
                            </label>
                        </div>

                        <div class="col s6 m3 l1" style="margin-bottom: 10px;">
                            <label class="row" for="size_custom">Tamanho</label>
                            <label class="switch">
                                <input type="checkbox" id="size_custom">
                                <div class="slider"></div>
                            </label>
                        </div>

                        <div class="col s12 m6 l2 relative hide" style="padding: 25px 0 0px 5px!important;"
                             id="size_container">
                            <input id="size" type="number" step="1" max="1000000" value="127" min="1" class="input">
                        </div>

                        <div class="col s12 m8 l6 padding-tiny hide" id="default_container">
                            <label for="default">Valor Padrão</label>
                            <input id="default" type="text" class="input">
                        </div>
                    </div>
                </div>
            </div>

            <div id="tpl-list-filter" class="hide col s12 filterTpl">
                <select class="filter col s12 m6"></select>
                <select class="filter_operator col s12 m2">
                    <option value="__$0__" selected>__$0__</option>
                    <option value="=">=</option>
                    <option value="!=">!=</option>
                    <option value="<="><=</option>
                    <option value=">=">>=</option>
                    <option value=">">></option>
                    <option value="<"><</option>
                    <option value="%%">%%</option>
                    <option value="%=">%=</option>
                    <option value="=%">=%</option>
                    <option value="!%%">!%%</option>
                    <option value="!%=">!%=</option>
                    <option value="!=%">!=%</option>
                    <option value='in'>in "1,2"</option>
                    <option value='!in'>! in "1,2"</option>
                </select>
                <input type="text" class="filter_value col s12 m4" style="padding-top: 13px;" value="__$1__">
            </div>
            <option id="optionTpl" class="hide" value="__$0____$2__">__$1__</option>

            <div class="hide card padding-medium" id="requireListFilter">
                <header class="row padding-small">
                    <span class="left padding-medium" style="padding-left: 0!important;">Filtrar Lista</span>
                    <button class="btn-floating color-blue opacity hover-opacity-off" onclick="addFilter()"><i
                                class="material-icons">add</i></button>
                </header>

                <div id="list-filter"></div>
            </div>


            <div class="hide card padding-medium" id="requireListExtend">
                <header class="row padding-small">
                    <span class="left padding-medium">Selecionar Opções de Campos Multiplos</span>
                </header>

                <p class="color-text-gray">esta entidade possúi campos com multiplos valores, marque para selecionar um
                    em específico.</p>

                <div id="requireListExtendDiv"></div>
            </div>

            <label class="col s12 relative tpl hide" for="__$0__" id="selectOneListOption">
                <input type="checkbox" id="__$0__" class="left padding-right __$2__"/>
                <span class="left padding-medium font-medium pointer">__$1__ </span>
            </label>

            <div class="requireName hide card padding-medium">
                <header class="row padding-small">
                    <span class="left padding-medium">Formulário</span>
                    <label class="switch">
                        <input type="checkbox" class="input" id="form">
                        <div class="slider"></div>
                    </label>
                </header>

                <input type="hidden" id="input" class="input"/>

                <div class="row hide form_body">
                    <div class="col s4 padding-small form_body">
                        <label>Colunas</label>
                        <select class="input form_body" id="cols">
                            <option value="12" selected>12/12</option>
                            <option value="11">11/12</option>
                            <option value="10">10/12</option>
                            <option value="9">9/12</option>
                            <option value="8">8/12</option>
                            <option value="7">7/12</option>
                            <option value="6">6/12</option>
                            <option value="5">5/12</option>
                            <option value="4">4/12</option>
                            <option value="3">3/12</option>
                            <option value="2">2/12</option>
                            <option value="1">1/12</option>
                        </select>
                    </div>

                    <div class="col s4 padding-small form_body">
                        <label>Colunas Table</label>
                        <select class="input form_body" id="colm">
                            <option value="" selected disabled></option>
                            <option value="12">12/12</option>
                            <option value="11">11/12</option>
                            <option value="10">10/12</option>
                            <option value="9">9/12</option>
                            <option value="8">8/12</option>
                            <option value="7">7/12</option>
                            <option value="6">6/12</option>
                            <option value="5">5/12</option>
                            <option value="4">4/12</option>
                            <option value="3">3/12</option>
                            <option value="2">2/12</option>
                            <option value="1">1/12</option>
                        </select>
                    </div>

                    <div class="col s4 padding-small form_body">
                        <label>Colunas Desktop</label>
                        <select class="input form_body" id="coll">
                            <option value="" selected disabled></option>
                            <option value="12">12/12</option>
                            <option value="11">11/12</option>
                            <option value="10">10/12</option>
                            <option value="9">9/12</option>
                            <option value="8">8/12</option>
                            <option value="7">7/12</option>
                            <option value="6">6/12</option>
                            <option value="5">5/12</option>
                            <option value="4">4/12</option>
                            <option value="3">3/12</option>
                            <option value="2">2/12</option>
                            <option value="1">1/12</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>

                    <div class="col s12 m6 padding-small form_body">
                        <label for="class">Class</label>
                        <input id="class" type="text" class="input form_body">
                    </div>
                    <div class="col s12 m6 padding-small form_body">
                        <label for="style">Style</label>
                        <input id="style" type="text" class="input form_body">
                    </div>
                    <div class="clearfix"><br></div>
                </div>
            </div>

            <div class="requireName hide card padding-medium">
                <header class="row padding-large">
                    <span class="left">Validação</span>
                    <i class="material-icons padding-left">check</i>
                </header>
                <div class="collapsible-body">
                    <div class="clearfix"></div>

                    <div class="col s12">
                        <label class="input-field col s12">
                            <span>Expressão Regular para Validação</span>
                            <input id="regex" type="text" class="input font-medium">
                        </label>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="requireName hide card padding-medium">
                <header class="row padding-medium">
                <span class="left padding-medium">
                    <i class="material-icons left">assignment</i>
                    <span class="left padding-left">Valores Permitidos &nbsp;&nbsp;</span>
                </span>
                    <span class="btn-floating left color-green" id="allowBtnAdd"
                          onclick="copy('#tplValueAllow', '#spaceValueAllow');$('#spaceValueAllow').find('.allow:first-child').find('.values').focus()">
                    <i class="material-icons">add</i>
                </span>
                </header>

                <div class="col s12 hide" id="format-source">
                    <div class="clearfix"></div>

                    <div class="col s12">
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="image"/>
                            <span>Imagens</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="video"/>
                            <span>Vídeos</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="audio"/>
                            <span>Audios</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="document"/>
                            <span>Doc.</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="compact"/>
                            <span>Compact.</span>
                        </label>
                        <label class="col s6 m2 relative">
                            <input type="checkbox" class="file-format" id="denveloper"/>
                            <span>Dev.</span>
                        </label>
                    </div>

                    <div class="panel">
                        <div class="col s12 hide" id="formato-image">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="image" id="all-image"/>
                                    <span>Todas</span>
                                </label>
                                <?php
                                $document = ["png", "jpg", "jpeg", "gif", "bmp", "tif", "tiff", "psd", "svg"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='image-format oneformat' rel='image' id='{$id}'/><span class='upper'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col s12 hide" id="formato-video">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="video" id="all-video"/>
                                    <span>Todos</span>
                                </label>
                                <?php
                                $document = ["mp4", "avi", "mkv", "mpeg", "flv", "wmv", "mov", "rmvb", "vob", "3gp", "mpg"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='video-format oneformat' rel='video' id='{$id}'/><span class='upper'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col s12 hide" id="formato-audio">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="audio" id="all-audio"/>
                                    <span>Todos</span>
                                </label>
                                <?php
                                $document = ["mp3", "aac", "ogg", "wma", "mid", "alac", "flac", "wav", "pcm", "aiff", "ac3"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='audio-format oneformat' rel='audio' id='{$id}'/><span class='upper'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col s12 hide" id="formato-document">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="document" id="all-document"/>
                                    <span>Todas</span>
                                </label>
                                <?php
                                $document = ["txt", "doc", "docx", "dot", "dotx", "dotm", "ppt", "pptx", "pps", "potm", "potx", "pdf", "xls", "xlsx", "xltx", "rtf"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='document-format oneformat' rel='document' id='{$id}'/><span class='upper'>{$id}</span></label>";
                                }

                                ?>
                            </div>
                        </div>
                        <div class="col s12 hide" id="formato-compact">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="compact" id="all-compact"/>
                                    <span>Todas</span>
                                </label>
                                <?php
                                $document = ["rar", "zip", "tar", "7z"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='compact-format oneformat' rel='compact' id='{$id}'/><span class='upper'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col s12 hide" id="formato-denveloper">
                            <div class="row padding-small"></div>
                            <div class="padding-medium row color-grey-light round">
                                <label class="col s6 m2 relative">
                                    <input type="checkbox" class="allformat" rel="denveloper" id="all-denveloper"/>
                                    <span>Todas</span>
                                </label>
                                <?php
                                $document = ["html", "css", "scss", "js", "tpl", "json", "xml", "md", "sql", "dll"];
                                foreach ($document as $id) {
                                    echo "<label class='col s6 m2 relative'><input type='checkbox' class='denveloper-format oneformat' rel='denveloper' id='{$id}'/><span class='upper'>{$id}</span></label>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col s12" id="spaceValueAllow"></div>

                <div class="col s12 font-medium hide allow" id="tplValueAllow">
                    <label class="input-field col s12 m4 padding-small">
                        <span>Valor</span>
                        <input class="values" type="number" min="1" max="99"
                               onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 49 && event.charCode <= 57">
                    </label>

                    <label class="input-field col s12 m8 padding-small">
                        <span>Nome</span>
                        <input class="names" type="text">
                    </label>
                </div>

                <div class="clearfix"></div>
            </div>

            <div class="clearfix"><br></div>

            <li style="display: none">
                <div class="collapsible-header"><i class="material-icons">whatshot</i>Metadados
                </div>
                <div class="collapsible-body">
                    <div class="clearfix"></div>

                    <div class="col s12 m6">
                        <div class="input-field col s12">
                            <input id="pref" placeholder="separe com vírgula" type="text"
                                   class="validate" ng-model="attr.prefixo">
                            <label for="pref">Prefixo</label>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="input-field col s12">
                            <input id="sulf" placeholder="separe com vírgula" type="text"
                                   class="validate" ng-model="attr.sulfixo">
                            <label for="sulf">Sulfixo</label>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </li>

            <div class="clearfix"><br><br><br><br></div>
        </div>
        <div class="clearfix"><br></div>
    </div>
    <?php

    $data['data']['content'] = ob_get_contents();
    ob_end_clean();
}