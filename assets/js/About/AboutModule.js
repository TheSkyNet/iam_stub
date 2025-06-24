const showdown = require("showdown");
var About = {
    list: [],
    current: [],

    load: function () {
        return m.request({
                method: "GET",
                url: "/files/cv.md",
                withCredentials: true,
            extract: function (response) {
                    return  response.response

                 }

            }
        ).then(function (result) {
            console.log(result)
            About.current = result
        })
    }
};

var AboutModule = {
    loading: true,
    oninit: () => {
        About.load().then(() =>{AboutModule.loading = false})
    },
    view: function () {
        if (About.loading) {
            return 'loading'
        }
        const converter = new showdown.Converter();

        const md = converter.makeHtml(About.current);

        return m("div", {class: 'container card '}, [

            m('a', {
                class: 'btn btn-info btn-download float-right',
                href: '/files/Kevin-Morton-CV.pdf'
            }, 'download my cv'),
            m('.card-block', m.trust(md)),
        ]);
    }
};
module.exports = {About, AboutModule}