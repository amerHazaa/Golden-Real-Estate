<?php
// ملف إنشاء الشقق

class PropertyCreate {
    public static function create_property_page() {
        $towers = get_posts(['post_type' => 'tower', 'posts_per_page' => -1]);

        echo '<div class="wrap">';
        echo '<h1>إنشاء نموذج شقة جديد</h1>';
        echo '<form action="' . admin_url('admin-post.php') . '" method="POST">';
        echo '<input type="hidden" name="action" value="create_property_model">';
        echo '<label for="model_name">اسم النموذج:</label>';
        echo '<input type="text" id="model_name" name="model_name" required><br>';
        echo '<label for="tower_id">البرج:</label>';
        echo '<select id="tower_id" name="tower_id" required>';
        foreach ($towers as $tower) {
            echo '<option value="' . esc_attr($tower->ID) . '">' . esc_html($tower->post_title) . '</option>';
        }
        echo '</select><br>';
        echo '<label for="room_count">عدد الغرف:</label>';
        echo '<input type="number" id="room_count" name="room_count" required><br>';
        echo '<label for="bathroom_count">عدد الحمامات:</label>';
        echo '<input type="number" id="bathroom_count" name="bathroom_count" required><br>';
        echo '<label for="area">مساحة الشقة:</label>';
        echo '<input type="number" id="area" name="area" required><br>';
        echo '<label for="layout">المخطط:</label>';
        echo '<input type="text" id="layout" name="layout" required><br>';
        echo '<label for="images">الصور:</label>';
        echo '<button type="button" class="button" id="upload_images_button">إضافة صور</button>';
        echo '<div id="images_container"></div>';
        echo '<input type="hidden" id="images" name="images" required><br>';
        echo '<label for="details">التفاصيل:</label>';
        echo '<textarea id="details" name="details" required></textarea><br>';
        echo '<input type="submit" value="إنشاء نموذج" class="button button-primary">';
        echo '</form>';
        echo '</div>';

        // إضافة السكريبت لفتح شاشة الوسائط وإضافة الصور
        echo '<script>
        jQuery(document).ready(function($) {
            if (typeof wp.media !== "undefined") {
                var frame;
                $("#upload_images_button").on("click", function(e) {
                    e.preventDefault();
                    if (frame) {
                        frame.open();
                        return;
                    }
                    frame = wp.media({
                        title: "إضافة صور",
                        button: {
                            text: "استخدام الصور"
                        },
                        multiple: true
                    });
                    frame.on("select", function() {
                        var attachments = frame.state().get("selection").map(function(attachment) {
                            attachment = attachment.toJSON();
                            return attachment.url;
                        });
                        $("#images").val(attachments.join(","));
                        $("#images_container").html("");
                        for (var i = 0; i < attachments.length; i++) {
                            $("#images_container").append("<img src=\'" + attachments[i] + "\' style=\'max-width: 100px; margin: 5px;\' />");
                        }
                    });
                    frame.open();
                });
            } else {
                console.log("wp.media is undefined");
            }
        });
        </script>';
    }

    public static function create_property_model() {
        if (!current_user_can('manage_options')) {
            wp_die(__('عذرًا، غير مسموح لك الوصول إلى هذه الصفحة.'));
        }

        $model_name = sanitize_text_field($_POST['model_name']);
        $tower_id = intval($_POST['tower_id']);
        $room_count = intval($_POST['room_count']);
        $bathroom_count = intval($_POST['bathroom_count']);
        $area = floatval($_POST['area']);
        $layout = sanitize_text_field($_POST['layout']);
        $images = sanitize_textarea_field($_POST['images']);
        $details = sanitize_textarea_field($_POST['details']);

        // إنشاء النموذج كـ Custom Post Type
        $post_data = [
            'post_title'    => $model_name,
            'post_type'     => 'model',
            'post_status'   => 'publish',
            'meta_input'    => [
                '_tower_id' => $tower_id,
                '_room_count' => $room_count,
                '_bathroom_count' => $bathroom_count,
                '_area' => $area,
                '_layout' => $layout,
                '_images' => $images,
                '_details' => $details,
            ],
        ];

        $model_id = wp_insert_post($post_data);

        if (!is_wp_error($model_id)) {
            // جلب عدد الأدوار للبرج
            $floors = intval(get_post_meta($tower_id, '_floors', true));

            // إنشاء الشقق كـ Custom Post Type
            for ($floor = 1; $floor <= $floors; $floor++) {
                $property_code = $tower_id . '-' . $model_id . '-' . $floor;
                $property_data = [
                    'post_title'    => $model_name . ' - ' . $property_code,
                    'post_type'     => 'property',
                    'post_status'   => 'publish',
                    'meta_input'    => [
                        '_model_id' => $model_id,
                        '_tower_id' => $tower_id,
                        '_property_code' => $property_code,
                        '_floor' => $floor,
                        '_status' => 'متاحة',
                    ],
                ];
                wp_insert_post($property_data);
            }
        }

        wp_redirect(admin_url('admin.php?page=gre_properties'));
        exit;
    }
}

add_action('admin_post_create_property_model', array('PropertyCreate', 'create_property_model'));
