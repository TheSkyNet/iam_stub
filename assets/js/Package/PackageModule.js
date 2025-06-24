

var Package = {
  list: [],
  current: [],
  loadList: function () {
    return m.request({
      method: "GET",
      url: "/api/v1/package",
      withCredentials: true,
    })
      .then(function (result) {
        Package.list = result
      })
  }, load: function (id) {
    return m.request({
      method: "GET",
      url: "/api/v1/package/" + id,
      withCredentials: true,
    })
      .then(function (result) {
        Package.current = result
      })
  }
};

var PackageList = {
  oninit: Package.loadList,
  view: function () {
    return m(".row .grid", Package.list.map(function (packages) {
      return m(".grid-item col-xs-12 col-sm-6 col-md-4 col-lg-3", [
        m('a', {class: "card", href:  'https://packagist.org/packages/' + packages.link.trim()}, [
            m('.packages-item-title', packages.name),
            m('.packages-item-slug', m('pre', {class: 'pre'}, packages.slug.trim())),
            m('.grid-item-slug .slug-'+ packages.lang.trim(), packages.lang.trim())
          ]
        )
      ])
    }))
  },

};
module.exports = {PackageList, Package}