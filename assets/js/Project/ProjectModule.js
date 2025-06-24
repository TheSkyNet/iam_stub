const extractColors = require("extract-colors").default;
const showdown = require('showdown')
const {loading} = require("../components/loading");
let Project = {
    list: [],
    current: {
        id: '',
        img: '',
        title: '',
        body: '',
        type: '',
    },
    loadList: function () {
        return m.request({
            method: "GET",
            url: "/api/v1/project",
            withCredentials: true,
        })
            .then(function (result) {
                Project.list = result
            })
    }, load: function (id) {
        return m.request({
            method: "GET",
            url: "/api/v1/project/" + id,
            withCredentials: true,
        })
            .then(function (result) {
                Project.current = result
            })
    }
};

let ProjectOne = {
    onremove: function () {
        ProjectOne.loading = true;
    },
    loading: true,
    color: '#fff',
    oninit: function (vnode) {
        Project.load(vnode.attrs.key).then(() => {
            ProjectOne.loading = false;
        })
    },
    updateColler: () => {
        extractColors(Project.current.img)
            .then((colors) => {
                colors.sort((a, b) => b.area - a.area);

                ProjectOne.color = colors[0]?.hex;
                m.redraw()
            })
            .catch(console.error)
    },
    view: function () {
        if (ProjectOne.loading) {
            return loading()

        }
        const converter = new showdown.Converter();
        const md = converter.makeHtml(Project.current.body);
        return m(".container .post-container", [
            m('div', {class: "card post", style: {'background-color': ProjectOne.color}},
                m('', [
                        m('div', {class: "col"},
                            m('div', {id: '', class: "post-img-top"},
                                m(
                                    'img', {
                                        id: 'thing_img',
                                        class: "img-fluid",
                                        src: Project.current.img,
                                        oncreate: ProjectOne.updateColler,
                                        onupdate: ProjectOne.updateColler
                                    }
                                ),
                                m('.grid-item-slug .slug-' + Project.current.type.trim(), Project.current.type.trim())
                            ),
                            m('.post-title', m('.h1', Project.current.title)),
                            m('.post-block', m('.post-section', m.trust(md)))
                        ),
                    ]
                ))
        ])
    },
    onupdate: function () {

    }
};

let ProjectList = {
    oninit: Project.loadList,
    view: function () {
        if (ProjectList.loading) {
            return 'loading'

        }
        return m(".card-columns", Project.list.map(function (project) {
            return m(m.route.Link, {class: "card", href: `/project/${project.id}`}, [
                    m('div', {class: "card-img-top"},
                        m(
                            'img', {
                                class: "img-fluid", src: project.img,
                            }
                        )
                    ),
                    m('.grid-item-slug .slug-' + project.type.trim(), project.type.trim())
                ]
            )

        }))
    },

};


module.exports = {ProjectList, Project, ProjectOne}

