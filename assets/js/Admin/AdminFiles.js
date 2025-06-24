


var AdminFile = {
    list: [],
    media :{
        title: '',
        file: '',
    },

    current: [],
    loadList: function () {
        return m.request({
            method: "GET",
            url: "/api/v1/file",
            withCredentials: true,
        })
            .then(function (result) {
               AdminFile.list = result
            })
    },
    load: function (id) {
        return m.request({
                method: "GET",
                url: "/api/v1/file/" + id,
                withCredentials: true,
            }
        ).then(function (result) {
            AdminFile.file = result
        })
    },
    addFile() {
        return m.request({
                method: "POST",
                url: "/api/v1/file/save",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminFile.media,
            }
        ).then(function (result) {
            AdminFile.loadList()
        })
    },
    updateFile() {
        return m.request({
                method: "POST",
                url: "/api/v1/file",
                headers: {
                    'X-CSRF-Token': this.CSRF,
                    'Accept': `application/json`
                },
                withCredentials: true,
                body: AdminFile.file,
            }
        ).then(function (result) {
            AdminFile.loadList()
        })
    }
};
var AdminFileList = {

    oninit: (vnode) => {
        if (vnode.attrs.id) {
            AdminFile.load(vnode.attrs.id)
        }
        AdminFile.loadList()

    },
    view: function (vnode) {
        return m("div", {"class": "row"},
            [
                m('div', {class: 'col-12'}, m("h1",
                    "Files"
                )),
                m("div", {class: 'col-12'},
                    m('div',
                        {
                            class: 'center-block',
                            oncreate: (vnode) => {
                                const pond = FilePond.create({
                                    multiple: false,
                                    name: 'filepond',
                                    server: {
                                        url: '/api/v1/file',
                                        /* process: '/process',
                                         revert: '/process',
                                         patch: "?patch=",
                                         /!* headers: {
                                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                          }*!/*/
                                    },
                                   /* chunkUploads: true*/
                                });
                                pond.on('processfile', (error, file) => {
                                    if (error) {
                                        console.log('Oh no');
                                        return;
                                    }
                                    AdminFile.media.title = file.file.name
                                    AdminFile.media.file = file.serverId
                                    console.log('File added', file);
                                    console.log('File added', this.media);
                                    AdminFile.addFile()
                                });

// Add it to the DOM
                                vnode.dom.appendChild(pond.element);
                            }
                        }
                    ),
                    m("button", {
                            "class": "btn", onclick: () => {
                                this.mediaService.newMedia(this.media)
                            }
                        },
                        "Save"
                    ),
                ),
                m("div", {class: 'col-12'},

                    m("div", {"class": "container"},
                        m("div", {"class": "row"},
                            m("div", {"class": "col-12"},
                                m("table", {"class": "table  table-responsive table-bordered"},
                                    [
                                        m("thead",
                                            m("tr",
                                                [

                                                    m("th", {"scope": "col"},
                                                        "File Name"
                                                    ),
                                                    m("th", {"scope": "col"},
                                                        "File Path"
                                                    ),
                                                    m("th", {"scope": "col"},
                                                        "File "
                                                    ),
                                                    m("th", {"scope": "col"},
                                                        "Actions"
                                                    )
                                                ]
                                            )
                                        ),
                                        m("tbody",
                                            AdminFile.list.map((file) => {
                                                console.log(file, 'E')
                                                return m("tr",
                                                    [

                                                        m("td",
                                                            file.filename
                                                        ),

                                                        m("td",
                                                            `/files/${file.filename}`
                                                        ),
                                                        m("td",
                                                            m('img', {src: `/files/${file.filename}`})
                                                        ),
                                                        m("td",
                                                            [
                                                                m(m.route.Link, {
                                                                        href: `/file/${file.id}`,
                                                                        class: "btn btn-primary",
                                                                        options: {replace: true},
                                                                    },
                                                                    m("i", {"class": "octicon octicon-eye"})
                                                                ),

                                                                m("button", {
                                                                        "class": "btn btn-success",
                                                                        "type": "button"
                                                                    },
                                                                    m("i", {"class": "octicon octicon-pencil "})
                                                                ),
                                                                m("button", {
                                                                        "class": "btn btn-danger",
                                                                        "type": "button"
                                                                    },
                                                                    m("i", {"class": "octicon octicon-trash"})
                                                                )
                                                            ]
                                                        )
                                                    ]
                                                )
                                            })
                                        )
                                    ]
                                )
                            )
                        )
                    )
                ),

            ]
        );


    },
    oncreate: function (vnode) {

        //$grid.masonry('layout');

// trigger initial layout
        //  $grid.masonry();
    },
    onupdate: function (vnode) {

    }
};


module.exports = {AdminFileList, AdminFile}