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
        case 'extendMult':
            return extendMult(attr);
            break;
        case 'list':
            return listSelect(attr);
            break;
        case 'listMult':
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

    return attr;
}

function select(attr) {

    return attr;
}

function status(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Status";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "status";

    return attr;
}

function password(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Senha";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "senha";

    return attr;
}

function cover(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Imagem";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "imagem";

    return attr;
}

function time(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Hora";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "hora";

    return attr;
}

function datatime(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Data";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "data";

    return attr;
}

function data(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Data";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "data";

    return attr;
}

function week(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Dias da Semana";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "dias_da_semana";

    return attr;
}

function link(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Url Name";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "urlname";
    attr['type'] = "link";

    return attr;
}

function title(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Titulo";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "titulo";
    attr['type'] = "title";

    return attr;
}

function listSelectMult(attr) {

    return attr;
}

function listSelect(attr) {

    return attr;
}

function extendMult(attr) {

    return attr;
}

function extend(attr) {

    return attr;
}

function primaryKey(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "id";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "id";
    attr['type'] = "pri";

    return attr;
}

function rsValue(attr) {
    attr['title'] = 'title' in attr && attr['title'] !== "" ? attr['title'] : "Valor";
    attr['slug'] = 'slug' in attr && attr['slug'] !== "" ? attr['slug'] : "valor";
    attr['type'] = "money";
    attr['size'] = "";
    attr['input'] = "money";
    attr['default'] = 0;

    return attr;
}

function inteiro(attr) {
    attr['type'] = "int";
    attr['size'] = 11;
    attr['input'] = "number";

    return attr;
}

function textfield(attr) {
    attr['type'] = "text";
    attr['size'] = "";
    attr['input'] = "textarea";

    return attr;
}

function texto(attr) {
    attr['type'] = "varchar";
    attr['size'] = 255;
    attr['input'] = "text";

    return attr;
}