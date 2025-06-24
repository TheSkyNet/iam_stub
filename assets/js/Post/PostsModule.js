var Post = {
  list: [],
  current: [],
  loadList: function() {
    return m.request({
      method: "GET",
      url: "/api/v1/post",
      withCredentials: true,
    })
      .then(function(result) {
        Post.list = result
      })
  },  load: function(id) {
    return m.request({
      method: "GET",
      url: "/api/v1/post/" + id,
      withCredentials: true,
    })
      .then(function(result) {
        Post.current = result
      })
  }
};

var PostList = {
  oninit:  Post.loadList(),
  view: function () {
    return m(".row", Post.list.map(function (post) {
      return m(".grid-item col-xs-12 col-sm-6 col-md-4 col-lg-3", [
        m('a',{class: "card", href: '/#!/'+post.type+'/'+post.id}, [
            m('div', {class: "card-img-top"},
              m(
                'img', {class: "img-fluid", src: post.img}
              )
            ),
            m('.grid-item-title', post.title)
          ]
        )
      ])
    }))
  }
};
module.exports = {PostList, Post}
