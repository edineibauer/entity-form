function defaultValuesEntity(type, attr) {

    if(type === "") {

    }

    return attr;
}

function funcaoValuesEntity(type, attr) {
    switch (type) {
        case 'text':
            return texto(attr);
            break;
        case 'textarea':
            return textfield(attr);
            break;
        case 'int':
            return inteiro(attr);
            break;
        case 'valor':
            return rsValue(attr);
            break;
        case 'pri':
            return primaryKey(attr);
            break;
        case 'extend':
            return extend(attr);
            break;
        case 'extend_mult':
            return extendMult(attr);
            break;
        case 'list':
            return listSelect(attr);
            break;
        case 'list_mult':
            return listSelectMult(attr);
            break;
        case 'title':
            return title(attr);
            break;
        case 'link':
            return link(attr);
            break;
        case 'date':
            return data(attr);
            break;
        case 'datetime':
            return datatime(attr);
            break;
        case 'time':
            return time(attr);
            break;
        case 'week':
            return week(attr);
            break;
        case 'cover':
            return cover(attr);
            break;
        case 'password':
            return password(attr);
            break;
        case 'status':
            return status(attr);
            break;
        case 'on':
            return on(attr);
            break;
        case 'select':
            return select(attr);
            break;
    }

    return attr;
}

function on(attr) {
    attr['list'] = 'list' in attr && attr['list'] !== "" ? attr['list'] : true;

    return attr;
}

function select(attr) {
    attr['list'] = false;

    return attr;
}

function status(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Status";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "status";
    attr['list'] = 'list' in attr && attr['list'] !== "" ? attr['list'] : true;

    return attr;
}

function password(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Senha";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "senha";
    attr['list'] = false;

    return attr;
}

function cover(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Imagem";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "imagem";
    attr['list'] = 'list' in attr && attr['list'] !== "" ? attr['list'] : true;

    return attr;
}

function time(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Hora";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "hora";
    attr['list'] = false;

    return attr;
}

function datatime(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Data";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "data";
    attr['list'] = false;

    return attr;
}

function data(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Data";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "data";
    attr['list'] = 'list' in attr && attr['list'] !== "" ? attr['list'] : true;

    return attr;
}

function week(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Dias da Semana";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "dias_da_semana";
    attr['list'] = 'list' in attr && attr['list'] !== "" ? attr['list'] : true;

    return attr;
}

function link(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Url Name";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "urlname";
    attr['list'] = false;

    return attr;
}

function title(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Titulo";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "titulo";
    attr['list'] = 'list' in attr && attr['list'] !== "" ? attr['list'] : true;

    return attr;
}

function listSelectMult(attr) {
    attr['list'] = false;

    return attr;
}

function listSelect(attr) {
    attr['list'] = false;

    return attr;
}

function extendMult(attr) {
    attr['list'] = false;

    return attr;
}

function extend(attr) {
    attr['list'] = false;

    return attr;
}

function primaryKey(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "id";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "id";
    attr['update'] = false;
    attr['list'] = false;

    return attr;
}

function rsValue(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Valor";
    attr['column'] = 'column' in attr && attr['column'] !== "" ? attr['column'] : "valor";
    attr['size'] = "";
    attr['input'] = "money";
    attr['default'] = 0;
    attr['list'] = 'list' in attr && attr['list'] !== "" ? attr['list'] : true;

    return attr;
}

function inteiro(attr) {
    attr['size'] = 11;
    attr['input'] = "number";
    attr['list'] = false;

    return attr;
}

function textfield(attr) {
    attr['size'] = "";
    attr['input'] = "textarea";
    attr['list'] = false;

    return attr;
}

function texto(attr) {
    attr['size'] = 255;
    attr['input'] = "text";
    attr['list'] = 'list' in attr && attr['list'] !== "" ? attr['list'] : true;

    return attr;
}