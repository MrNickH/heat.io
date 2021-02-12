function getUrlComponentsAsArray() {
    return window.location.pathname.split('/');
}

function getUrlComponent(id) {
    return getUrlComponentsAsArray()[id];
}