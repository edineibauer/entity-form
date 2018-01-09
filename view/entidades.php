<ul id="nav-entity" class="color-text-white z-depth-4">
    <div class="row color-blue">
        <div class="panel">
            <div class="col s7 upper padding-medium">
                Entidades
            </div>
            <div class="col s5 right-align">
                <a class="btn color-white btn-floating right" id="newEntityBtn" onclick="entityEdit()">
                    <i class="material-icons">add</i>
                </a>
            </div>
        </div>
    </div>
    <ul class="row">
        <li class="col s12" id="entity-space">
            <div class="col s12 hide" id="tpl-entity">
                <div class="col s7 padding-small">{{$}}</div>
                <div class="padding-small col s5 right-align">
                    <a class="pointer right padding-tiny" onclick="removeEntity('{{$}}')">
                        <i class="material-icons white-text font-size12">delete</i>
                    </a>
                    <a class="pointer right padding-tiny" onclick="entityEdit('{{$}}')">
                        <i class="material-icons white-text font-size12">edit</i>
                    </a>
                </div>
            </div>
        </li>
    </ul>
</ul>

<form class="col s12 m4 z-depth-2" id="nav-menu">
    <header class="row">
        <div class="panel">
            <div class="col s12 padding-tiny" ng-show="attr.type">
                <button class="btn color-blue left" type="submit" id="saveAttrBtn" name="action" onclick="saveEntity()">
                    salvar
                    <i class="material-icons right padding-left">check</i>
                </button>
                <button class="btn color-grey right" type="submit" title="Novo Atributo" id="saveAttrBtn" name="action"
                        onclick="editAttr()">
                    <i class="material-icons right">add</i>
                </button>
            </div>
        </div>
    </header>
    <div class="row"></div>
    <div class="panel">
        <div class="row">
            <label class="col s12">
                <span>Nome da Entidade</span>
                <input id="entityName" type="text" placeholder="entidade..." class="font-size17">
            </label>
        </div>
        <div class="col s12"><br></div>
        <ul class="row" id="entityAttr"></ul>
        <li class="col s12 hide" id="tpl-attrEntity">{{$1}}
            <a class="waves-effect waves-red btn-flat" onclick="deleteAttr({{$0}})"><i class="material-icons right">delete</i></a>
            <a class="waves-effect waves-red btn-flat" onclick="editAttr({{$0}})"><i
                        class="material-icons right">edit</i></a>
            <a onclick="downAttr({{$0}})" class="waves-effect waves-teal btn-flat"><i class="material-icons right">arrow_downward</i></a>
            <a onclick="upAttr({{$0}})" class="waves-effect waves-teal btn-flat"><i class="material-icons right">arrow_upward</i></a>
        </li>
    </div>
</form>

<div id="main" class="row color-gray-light">
    <div class="col s12">
        <div class="card padding-medium">
            <div class="row">
                <div class="col s12 m4 padding-small pad">
                    <label class="row" for="funcaoPrimary">Genérico</label>
                    <select class="input selectInput" id="funcaoPrimary">
                        <option value="" disabled selected>Input Genérica</option>
                        <option value="text">Texto</option>
                        <option value="textarea">Área de Texto</option>
                        <option value="int">Inteiro</option>
                        <option value="float">Float</option>
                        <option value="boolean">Boleano</option>
                        <option value="select">Select</option>
                        <option value="radio">Radio</option>
                        <option value="checkbox">CheckBox</option>
                        <option value="range">Range</option>
                        <option value="color">Cor</option>
                        <option value="source">Arquivo</option>
                    </select>
                </div>
                <div class="col s12 m4 padding-small">
                    <label class="row" for="funcaoIdentifier">Semântico</label>
                    <select class="input selectInput" id="funcaoIdentifier">
                        <option value="" disabled selected>Input de Identidade</option>
                        <option value="identifier">Identificador</option>
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
                    <select class="input selectInput" id="funcaoRelation">
                        <option value="" disabled selected>Input Relacional</option>
                        <option value="extend">Extensão</option>
                        <option value="extend_mult">Extensão Multipla</option>
                        <option value="list">Lista</option>
                        <option value="list_mult">Lista Multipla</option>
                    </select>
                </div>
            </div>

            <div class="col s12">
                <div class="col s12 m8 l9 padding-small hide" id="nomeAttr">
                    <label for="nome">Nome do Atributo</label>
                    <input id="nome" autocomplete="off" type="text" class="input">
                </div>
                <div class="col s12 m4 l3 requireName hide">
                    <div class="col s12 m6">
                        <label class="row" for="unique">Único</label>
                        <label class="switch">
                            <input type="checkbox" class="input" id="unique">
                            <div class="slider"></div>
                        </label>
                    </div>

                    <div class="col s12 m6">
                        <label class="row" for="update">Update</label>
                        <label class="switch">
                            <input type="checkbox" class="input" id="update">
                            <div class="slider"></div>
                        </label>
                    </div>
                </div>

                <div class="row requireName hide">

                    <div class="col s12">
                        <div class="col s12 m4 l2 padding-small">
                            <label class="row" for="null">Nulo</label>
                            <label class="switch">
                                <input type="checkbox" class="input" id="null">
                                <div class="slider"></div>
                            </label>
                        </div>
                        <div class="col s12 m8 l10 padding-tiny" id="default-container">
                            <label for="default">Valor Padrão</label>
                            <input id="default" type="text" class="input">
                        </div>
                    </div>

                    <div class="col s12">
                        <div class="col s12 m4 l2 padding-small">
                            <label class="row" for="size_custom">Tamanho</label>
                            <label class="switch">
                                <input type="checkbox" id="size_custom" class="input">
                                <div class="slider"></div>
                            </label>
                        </div>
                        <div class="col s12 m8 l10 relative" style="padding: 22px 0 22px 5px!important;"
                             id="size-container">
                            <input id="size" type="number" step="1" max="1000000" value="127" min="1" class="input">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="requireName hide card padding-medium">
            <header class="row padding-small">
                <span class="left padding-medium">Formulário</span>
                <label class="switch">
                    <input type="checkbox" class="input" id="show_ui">
                    <div class="slider"></div>
                </label>
            </header>

            <div class="row hide" id="show_ui_body">
                <div class="col s4 padding-small">
                    <label>Colunas</label>
                    <select class="input" id="cols">
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

                <div class="col s4 padding-small">
                    <label>Colunas Table</label>
                    <select class="input" id="colm">
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

                <div class="col s4 padding-small">
                    <label>Colunas Desktop</label>
                    <select class="input" id="coll">
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

                <div class="col s12 m6 padding-small">
                    <label for="class">Class</label>
                    <input id="class" type="text" class="input">
                </div>
                <div class="col s12 m6 padding-small">
                    <label for="style">Style</label>
                    <input id="style" type="text" class="input">
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
                        <input id="regular" type="text" class="font-size15">
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
                      onclick="cloneTo('#tplValueAllow', '#spaceValueAllow')">
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
                        <div class="pd-mediumb row color-grey-light round">
                            <label class="col s6 m2 relative">
                                <input type="checkbox" class="allformat" rel="image" id="all-image"
                                       checked='checked'/>
                                <span>Todas</span>
                            </label>
                            <?php
                            $document = ["png", "jpg", "jpeg", "gif", "bmp", "tif", "tiff", "psd", "svg"];
                            foreach ($document as $id) {
                                echo "<label class='col s6 m2 relative'><input type='checkbox' checked='checked' class='image-format oneformat' rel='image' id='{$id}'/><span class='upper'>{$id}</span></label>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col s12 hide" id="formato-video">
                        <div class="row padding-small"></div>
                        <div class="pd-mediumb row color-grey-light round">
                            <label class="col s6 m2 relative">
                                <input type="checkbox" class="allformat" rel="video" id="all-video"
                                       checked='checked'/>
                                <span>Todos</span>
                            </label>
                            <?php
                            $document = ["mp4", "avi", "mkv", "mpeg", "flv", "wmv", "mov", "rmvb", "vob", "3gp", "mpg"];
                            foreach ($document as $id) {
                                echo "<label class='col s6 m2 relative'><input type='checkbox' checked='checked' class='video-format oneformat' rel='video' id='{$id}'/><span class='upper'>{$id}</span></label>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col s12 hide" id="formato-audio">
                        <div class="row padding-small"></div>
                        <div class="pd-mediumb row color-grey-light round">
                            <label class="col s6 m2 relative">
                                <input type="checkbox" class="allformat" rel="audio" id="all-audio"
                                       checked='checked'/>
                                <span>Todos</span>
                            </label>
                            <?php
                            $document = ["mp3", "aac", "ogg", "wma", "mid", "alac", "flac", "wav", "pcm", "aiff", "ac3"];
                            foreach ($document as $id) {
                                echo "<label class='col s6 m2 relative'><input type='checkbox' checked='checked' class='audio-format oneformat' rel='audio' id='{$id}'/><span class='upper'>{$id}</span></label>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col s12 hide" id="formato-document">
                        <div class="row padding-small"></div>
                        <div class="pd-mediumb row color-grey-light round">
                            <?php
                            $document = ["txt", "doc", "docx", "dot", "dotx", "dotm", "ppt", "pptx", "pps", "potm", "potx", "pdf", "xls", "xlsx", "xltx", "rtf"];
                            foreach ($document as $id) {
                                echo "<label class='col s6 m2 relative'><input type='checkbox' checked='checked' class='document-format' id='{$id}'/><span class='upper'>{$id}</span></label>";
                            }

                            ?>
                        </div>
                    </div>
                    <div class="col s12 hide" id="formato-compact">
                        <div class="row padding-small"></div>
                        <div class="pd-mediumb row color-grey-light round">
                            <?php
                            $document = ["rar", "zip", "tar", "7z"];
                            foreach ($document as $id) {
                                echo "<label class='col s6 m2 relative'><input type='checkbox' checked='checked' class='compact-format' id='{$id}'/><span class='upper'>{$id}</span></label>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col s12 hide" id="formato-denveloper">
                        <div class="row padding-small"></div>
                        <div class="pd-mediumb row color-grey-light round">
                            <?php
                            $document = ["html", "css", "scss", "js", "tpl", "json", "xml", "md", "sql", "dll"];
                            foreach ($document as $id) {
                                echo "<label class='col s6 m2 relative'><input type='checkbox' checked='checked' class='denveloper-format' id='{$id}'/><span class='upper'>{$id}</span></label>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col s12" id="spaceValueAllow"></div>

            <div class="col s12 hide allow" id="tplValueAllow">
                <span class="input-field col s12 m4 padding-small">
                    <label for="allow">Valor</label>
                    <input id="allow" type="text">
                </span>

                <div class="input-field col s12 m8 padding-small">
                    <label for="allowRelated">Nome (Label)</label>
                    <input id="allowRelated" type="text">
                </div>
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
    </div>
    <div class="clearfix"><br></div>
</div>