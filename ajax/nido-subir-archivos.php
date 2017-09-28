<?php
    $parent_post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;  // The parent ID of our attachments
    $valid_formats = array( 
        "jpg", 
        "png", 
        "gif", 
        "bmp", 
        "jpeg", 
        "pdf", 
        "docx", 
        "doc",
        "xlsx",
        "xls",
        "ppt",
        "pptx" ); // Supported file types
    $max_file_size = 1024 * 1000; // in kb
    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['path'] . '/';
    $count = 0;

    $attachments = get_posts( array(
        'post_type'         => 'attachment',
        'posts_per_page'    => -1,
        'post_parent'       => $parent_post_id,
        'exclude'           => get_post_thumbnail_id() // Exclude post thumbnail to the attachment count
    ) );

    // Image upload handler
    if( $_SERVER['REQUEST_METHOD'] == "POST" ) {
            
        foreach ( $_FILES['files']['name'] as $f => $name ) {
            $extension = pathinfo( $name, PATHINFO_EXTENSION );
            // Generate a randon code for each file name
            $new_filename = wp_unique_filename( wp_upload_dir()['path'], $name );
            
            if ( $_FILES['files']['error'][$f] == 4 ) {
                continue; 
            }
            
            if ( $_FILES['files']['error'][$f] == 0 ) {
                // Check if image size is larger than the allowed file size
                if ( $_FILES['files']['size'][$f] > $max_file_size ) {
                    wp_send_json_error( $name . ' is too large!' );
                    $upload_message[] = "$name is too large!.";
                    continue;
                
                // Check if the file being uploaded is in the allowed file types
                } elseif( ! in_array( strtolower( $extension ), $valid_formats ) ){
                    $upload_message[] = "$name is not a valid format";
                    wp_send_json_error( $name . ' is not a valid format' ); 
                    continue; 
                
                } else{ 
                    // If no errors, upload the file...
                    if( move_uploaded_file( $_FILES["files"]["tmp_name"][$f], $path.$new_filename ) ) {
                        
                        $count++; 

                        $filename = $path.$new_filename;
                        $filetype = wp_check_filetype( basename( $filename ), null );
                        $wp_upload_dir = wp_upload_dir();
                        $attachment = array(
                            'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        );
                        // Insert attachment to the database
                        $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

                        require_once( ABSPATH . 'wp-admin/includes/image.php' );
                        
                        // Generate meta data
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename ); 
                        wp_update_attachment_metadata( $attach_id, $attach_data );
                        
                    }
                }
            }
        }
    }
     
    wp_send_json_success( array( wp_upload_dir()['url'] . '/' . $new_filename, $extension ) );