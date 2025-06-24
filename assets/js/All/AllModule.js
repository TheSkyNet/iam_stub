var All = {
    list: [],
    current: [],
    loadList: function () {
        return m.request({
            method: "GET",
            url: "/api/v1/all",
            withCredentials: true,
        })
            .then(function (result) {
                All.list = [...result[1], ...result[0], ...result[2], ...result[3]];
                shuffleArray(All.list);

            })
    },
    load: function (id) {
        return m.request({
                method: "GET",
                url: "/api/v1/all/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            All.current = result
        })
    }
};

var AllList = {
    oninit: All.loadList(),
    view: function () {

        return m(".card-columns", All.list.map(function (project) {
            if (project.type === 'package') {
                return packageView(project)
            }
            if (project.type === 'project') {
                return projectView(project)
            }
            if (project.type === 'post') {
                return postView(project)
            }
            if (project.type === 'code') {
                return codeView(project)
            }
        }))
    },

};

function postView(post) {
    return m(".card", [
        m(m.route.Link, {class: "card", href:  post.type + '/' + post.id}, [
                m('div', {class: "card-img-top"},
                    m(
                        'img', {class: "img-fluid", src: post.img}
                    )
                ),
                m('.grid-item-title', post.title),
                m('.grid-item-slug .slug-' + post.type.trim(), post.type.trim())

            ]
        )
    ])
}

function codeView(code) {
    return m(".card", [
        m(m.route.Link, {class: "card", href:  '/code/' + code.id}, [
                m('div', {class: "card-img-top"},
                    m(
                        'img', {class: "img-fluid", src: code.img}
                    )
                ),
                m('.grid-item-title', code.title),
                m('.grid-item-slug .slug-' + code.type.trim(), code.type.trim())

            ]
        )
    ])
}

function projectView(project) {
    return m(".card ", [
        m(m.route.Link, {class: "", href:  project.type + '/' + project.id}, [
                m('div', {class: "card-img-top"},
                    m(
                        'img', {class: "img-fluid", src: project.img}
                    )
                ),
                /* m('.grid-item-title', project.title),*/
                m('.grid-item-slug .slug-' + project.type.trim(), project.type.trim())
            ]
        )
    ])
}

function packageView(packages) {
    return m(".card", [
        m('a', {class: "card", href: 'https://packagist.org/packages/' + packages.link.trim()}, [
                m('.packages-item-title', packages.name),
                m('.packages-item-slug', m('pre', {class: 'pre'}, packages.slug.trim())),
                m('.grid-item-slug .slug-' + packages.lang.trim(), packages.lang.trim())
            ]
        )
    ])
}

function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}

module.exports = {AllList, All, packageView , projectView, codeView, postView}

