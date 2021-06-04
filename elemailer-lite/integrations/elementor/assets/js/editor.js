(function ($) {

    // call the the functionality of add, edit form when elementor editor panel is open for edit
    elementor.hooks.addAction('panel/open_editor/widget/elemailer-selected-posts', function (panel, model, view) {
        //call initially to set the already saved data
        elemailer_get_taxonomy_for_selected_posts();

        //function to get taxonomy based on post type 
        function elemailer_get_taxonomy_for_selected_posts(onload = true) {
            // console.log('get taxonomy');
            var elTaxonomy = $('[data-setting="taxonomy_type"]');

            elTaxonomy.empty();
            //only trigger change to reset selected taxonomy option when post type is actively changed
            if (onload == false && event.type == 'change') {
                //this is needed to reset the selected taxonomy
                elTaxonomy.trigger('change');
            }
            var post_type = $('[data-setting="post_type"]').val() || model.attributes.settings.attributes.post_type || [];
            var data = {
                action: 'elemailer_get_taxonomies',
                postTypeNonce: elemailer_lite.wpRestNonce,
                post_type: post_type
            };

            var i = 0;
            $.post(elemailer_lite.ajaxUrl, data, function (response) {
                var taxonomy_name = JSON.parse(response);
                $.each(taxonomy_name, function () {
                    if (this.name == 'post_format') {
                        return;
                    }

                    // console.log('taxonomy loop: '+(i++));
                    elTaxonomy.append('<option value="' + this.name + '">' + this.name + '</option>');

                });
                //set already selected value
                elTaxonomy.val(model.attributes.settings.attributes.taxonomy_type);
                elemailer_get_posts_for_selected_posts(elTaxonomy);

                if (elTaxonomy.has('option').length == 0) {
                    elTaxonomy.attr('disabled', 'disabled');
                } else {
                    elTaxonomy.removeAttr('disabled');
                }
            });//$.post                
        }//elemailer_get_taxonomy_for_selected_posts()

        //function to get posts based on taxonomy
        function elemailer_get_posts_for_selected_posts(onload = true) {
            // console.log('get posts');
            var elPostSelect = $('[data-setting="post_select"]');
            //only trigger change to reset selected posts option when taxonomy is actively changed
            if (event.type == 'change') {
                elPostSelect.trigger('change');
            }
            if (typeof (onload) !== 'object') {
                //var taxonomy_type = $('[data-setting="taxonomy_type"]').val();
                var taxonomy_type = onload;
            } else {
                var taxonomy_type = onload.val();
                elPostSelect.empty();
            }

            //if no taxonomy selected stop the function to avoid showing null value in posts
            if (taxonomy_type == null) {
                return;
            }
            var post_type = $('[data-setting="post_type"]').val() || model.attributes.settings.attributes.post_type || [];
            var data = {
                action: 'elemailer_get_posts',
                postTypeNonce: elemailer_lite.wpRestNonce,
                taxonomy_type: taxonomy_type,
                post_type: post_type
            };

            var j = 0;
            $.post(elemailer_lite.ajaxUrl, data, function (response) {
                var posts = JSON.parse(response);
                $.each(posts, function (idx, value) {
                    // console.log('posts loop: '+(j++));                    
                    elPostSelect.append('<option value="' + idx + '">' + value + '</option>');
                });
                //set already selected value
                if (typeof (onload) === 'object') {
                    elPostSelect.val(model.attributes.settings.attributes.post_select);
                }
            });

        }//elemailer_get_posts_for_selected_posts()

        //when moving from Advanced tab to content model variable is null so to pass it's data
        function elemailer_pass_around_model_for_selected_posts(panel, model, view) {
            // console.log('pass around the model');
            // set timeout to load content tab's content
            setTimeout(function () {
                elemailer_get_taxonomy_for_selected_posts();
            }, 100);
        }

        //get taxonomy
        $('#elementor-controls').on('change', '[data-setting="post_type"]', function (event) {
            // console.log('post change event');
            // pass onload value false, means the value was actively changed  
            elemailer_get_taxonomy_for_selected_posts(false);
            $('[data-setting="taxonomy_type"]').selectedIndex = -1;
            return true;
        });
        //get posts
        $('#elementor-controls').on('change', '[data-setting="taxonomy_type"]', function () {
            // console.log('taxonomy change event');  
            //pass $this to keep the changes to each different taxonomy
            elemailer_get_posts_for_selected_posts($(this));
            $('[data-setting="post_select"]').selectedIndex = -1;
            return true;
        });

        //this ensures the data remains the same even after switching back from advanced tab to content tab
        $(".elementor-panel").mouseenter(function () {
            elemailer_pass_around_model_for_selected_posts(panel, model, view);

        });

    });

    // call the the functionality of add, edit form when elementor editor panel is open for edit
    elementor.hooks.addAction('panel/open_editor/widget/elemailer-latest-posts', function (panel, model, view) {
        //call initially to set the already saved data
        elemailer_get_taxonomy_for_latest_posts();

        //function to get taxonomy based on post type 
        function elemailer_get_taxonomy_for_latest_posts(onload = true) {
            var elTaxonomy = $('[data-setting="taxonomy_type"]');

            elTaxonomy.empty();
            //only trigger change to reset selected taxonomy option when post type is actively changed
            if (onload == false && event.type == 'change') {
                //this is needed to reset the selected taxonomy
                elTaxonomy.trigger('change');
            }
            var post_type = $('[data-setting="post_type"]').val() || model.attributes.settings.attributes.post_type || [];
            var data = {
                action: 'elemailer_get_taxonomies',
                postTypeNonce: elemailer_lite.wpRestNonce,
                post_type: post_type
            };

            $.post(elemailer_lite.ajaxUrl, data, function (response) {
                var taxonomy_name = JSON.parse(response);
                $.each(taxonomy_name, function () {
                    if (this.name == 'post_format') {
                        return;
                    }

                    elTaxonomy.append('<option value="' + this.name + '">' + this.name + '</option>');

                });
                //set already selected value
                elTaxonomy.val(model.attributes.settings.attributes.taxonomy_type);
                elemailer_get_terms_for_latest_posts(elTaxonomy);

                if (elTaxonomy.has('option').length == 0) {
                    elTaxonomy.attr('disabled', 'disabled');
                } else {
                    elTaxonomy.removeAttr('disabled');
                }
            });//$.post                
        }//elemailer_get_taxonomy_for_latest_posts()

        //function to get terms based on taxonomy
        function elemailer_get_terms_for_latest_posts(onload = true) {
            var elPostSelect = $('[data-setting="terms"]');
            //only trigger change to reset selected terms option when taxonomy is actively changed
            if (event.type == 'change') {
                elPostSelect.trigger('change');
            }
            if (typeof (onload) !== 'object') {
                //var taxonomy_type = $('[data-setting="taxonomy_type"]').val();
                var taxonomy_type = onload;
            } else {
                var taxonomy_type = onload.val();
                elPostSelect.empty();
            }

            //if no taxonomy selected stop the function to avoid showing null value in terms
            if (taxonomy_type == null) {
                return;
            }
            var post_type = $('[data-setting="post_type"]').val() || model.attributes.settings.attributes.post_type || [];
            var data = {
                action: 'elemailer_get_terms',
                postTypeNonce: elemailer_lite.wpRestNonce,
                taxonomy_type: taxonomy_type,
                post_type: post_type
            };

            $.post(elemailer_lite.ajaxUrl, data, function (response) {
                var terms = JSON.parse(response);
                $.each(terms, function (idx, value) {
                    elPostSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                });
                //set already selected value
                if (typeof (onload) === 'object') {
                    elPostSelect.val(model.attributes.settings.attributes.terms);
                }
            });

        }//elemailer_get_terms_for_latest_posts()

        //when moving from Advanced tab to content model variable is null so to pass it's data
        function elemailer_pass_around_model_for_latest_posts(panel, model, view) {
            // set timeout to load content tab's content
            setTimeout(function () {
                elemailer_get_taxonomy_for_latest_posts();
            }, 100);
        }

        //get taxonomy
        $('#elementor-controls').on('change', '[data-setting="post_type"]', function (event) {
            // pass onload value false, means the value was actively changed  
            elemailer_get_taxonomy_for_latest_posts(false);
            $('[data-setting="taxonomy_type"]').selectedIndex = -1;
            return true;
        });
        //get terms
        $('#elementor-controls').on('change', '[data-setting="taxonomy_type"]', function () {
            //pass $this to keep the changes to each different taxonomy
            elemailer_get_terms_for_latest_posts($(this));
            $('[data-setting="post_select"]').selectedIndex = -1;
            return true;
        });

        //this ensures the data remains the same even after switching back from advanced tab to content tab
        $(".elementor-panel").mouseenter(function () {
            elemailer_pass_around_model_for_latest_posts(panel, model, view);
        });

    });

    elementor.hooks.addAction('panel/open_editor/column', function (panel, model, view) {
        elemailer_hide_unwanted_column_controls();

        function elemailer_hide_unwanted_column_controls() {
            var parentLayoutControls = panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-controls');
            panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('.elementor-panel-navigation').find('.elementor-tab-control-style').hide();
            var layoutControlsChildrens = parentLayoutControls.children();
            layoutControlsChildrens.css('display', 'none');
            //layoutControlsChildrens.eq(0).css('display', 'block');
            layoutControlsChildrens.eq(2).css('margin-top', '10px');
            layoutControlsChildrens.eq(2).css('padding-top', '20px');
            layoutControlsChildrens.eq(2).css('display', 'block');
            layoutControlsChildrens.eq(2).find('.elementor-control-responsive-switchers').hide();
        }

    });

    elementor.hooks.addAction('panel/open_editor/section', function (panel, model, view) {
        elemailer_hide_unwanted_section_controls();

        function elemailer_hide_unwanted_section_controls() {
            // panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('.elementor-panel-navigation').find('.elementor-tab-control-layout').removeClass('elementor-active');
            panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('.elementor-panel-navigation').find('.elementor-tab-control-style').find('a').trigger('click');
            panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('.elementor-panel-navigation').find('.elementor-tab-control-layout').hide();

            var parentLayoutControls = panel.$el.find('#elementor-panel-content-wrapper').find('#elementor-panel-page-editor').find('#elementor-controls');
            var layoutControlsChildrens = parentLayoutControls.children();
            layoutControlsChildrens.eq(0).css('display', 'none');
            layoutControlsChildrens.eq(1).css('display', 'none');
            layoutControlsChildrens.eq(2).css('display', 'none');

            layoutControlsChildrens.eq(-4).css('display', 'none');
            layoutControlsChildrens.eq(-3).css('display', 'none');
            layoutControlsChildrens.eq(-2).css('display', 'none');
            layoutControlsChildrens.eq(-1).css('display', 'none');
            layoutControlsChildrens.eq(-7).css('margin-top', '10px');
            layoutControlsChildrens.eq(-7).css('padding-top', '20px');
        }

    });

})(jQuery);
