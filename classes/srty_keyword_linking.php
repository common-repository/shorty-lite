<?php

/**
 * general function & static page
 */
class Srty_keyword_linking extends Srty_core {

    public function __construct() 
    {
        parent::__construct();
    }

    //redirect the keyword to destination url
    public function redirect()
    {

        $request_uri = preg_replace('#/$#', '', urldecode($_SERVER['REQUEST_URI']));
        $tracking_domain = get_option(SH_PREFIX . 'settings_tracking_domain');
        $tracking = preg_replace('#^/' . $tracking_domain . '/#', '', $request_uri);
        
        $link = $this->_by_tracking($tracking);
        if ($link !== NULL) {
            $this->view_data['link'] = $link;
            /**
             * check for viral bar
             */
            if ((bool) $link->cloaking_status_enable) {
                //check if basic cloak or viral bar
                if ($link->cloaking_type == SHORTLY_CLOAKING_TYPE_BASIC) {
                    $this->view('v_viral_basic', $this->view_data, FALSE);
                } else {
                    //check if viral on top or bottom
                    if ($link->bar_position == SHORTLY_BAR_POSITION_TOP) {
                        $this->view('v_viral_top', $this->view_data, FALSE);
                    } else {
                        //bottom
                        $this->view('v_viral_bottom', $this->view_data, FALSE);
                    }
                }
                      
                exit;
            } else {
                //redirect
                wp_redirect($link->destination_url, $link->link_redirect_type);
                exit;
            }
        }
    }

    //retrieve link data filtered by tracking
    private function _by_tracking($tracking = FALSE)
    {
        $table_name = $this->wpdb->prefix . SH_PREFIX . 'links';

        if ($tracking !== FALSE) {
            return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $table_name WHERE tracking_link=%s", $tracking));
        } else {
            return FALSE;
        }
    }
    
    //javasript for random choose
    public function keyword_linking_js() {
         $link = $this->_get_all();
         $this->view_data['link'] = $link;
        ?>
        <script type="text/javascript">
            $.fn.replacetext = function (target, replacement, max) {
                var limit = max || -1;
                var $textNodes = this.find("*")
                        .andSelf()
                        .contents()
                        .filter(function () {
                            if (this.nodeType === 3 && !$(this).parent("a").length) {
                                return true;
                            } else if (this.nodeType === 3 && !$(this).parent("img").length) {
                                return true;
                            }
                            return false;
                        });
                var matchCount = 0;
                /**
                 * count how many keyword that we found
                 */
                $textNodes.each(function (index, element) {
                    var $element = $(element);
                    var words = $element.text().split(/\b/);

                    $.each(words, function (index, word) {
                        if ((word !== '') && word.match(target)) {
                            matchCount++;
                        }
                    });
                });


                var references = selectReferences(matchCount, limit);

                var matches = 0;
                var matchIndex = -1;
                $textNodes.each(function (index, element) {
                    var $element = $(element);
                    var words = $element.text().split(/\b/);

                    var text = words.map(function (word, index) {
                        if (matches >= limit) {
                            return word;
                        }

                        if (word.match(target)) {
                            ++matchIndex;
                            if (!references || references.includes(matchIndex)) {
                                ++matches;
                                return word.replace(target, replacement);
                            }
                        }

                        return word;
                    });
                    $element.replaceWith(text.join(''));
                });

                function selectReferences(matchCount, limit) {
                    if (matchCount <= limit) {
                        return null;
                    }
                    var references = new Array(matchCount);
                    var i;
                    for (i = 0; i < matchCount; ++i) {
                        references[i] = i;
                    }

                    shuffle(references);

                    references = references.slice(0, limit);

                    return references;
                }

                /**
                 * suffle the array
                 * @param {type} o
                 * @returns {@var;x|RegExp.fn.replacetext.shuffle.o}
                 */
                function shuffle(o) {
                    for (var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x) {
                    }
                    return o;
                }
            };
        <?php
        $key = explode(",", $link->meta_keyword);
                ?>
                    $("#content").replacetext(/\b<?php echo trim($key); ?>\b/gi, "<a target='_blank' href='<?php echo redirect(); ?>'>$&</a>", 1);
        </script>

        <?php
    
    }
    
    private function _get_all()
    {
        $table_name = $this->wpdb->prefix . SH_PREFIX . 'links';
        return $this->wpdb->get_results("SELECT * FROM $table_name ");
    }
    
    //retrieve post content
    private function _get_post()
    {
        $query = new WP_Query(array('post_type' => 'page'));
        $posts = $query->get_posts();

        return $posts;
    }
}
