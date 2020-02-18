define(
    [
        'underscore',
        'pim/form',
        'pim/user-context',
        'theakademyui/template/username'
    ],
    function (
        _,
        BaseForm,
        UserContext,
        myTemplate
    ) {
            return BaseForm.extend({
                template: _.template(myTemplate),
                render: function () {
                    this.$el.html(
                        this.template({
                            username: UserContext.get('username')
                        })
                    )
                }
            });
        }
);