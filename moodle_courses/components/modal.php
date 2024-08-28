<?php

/**
 * Generates the modal HTML content.
 *
 * @return string HTML content for the modal.
 */
function generate_modal($modallabel = '', $modaldescription, $modallink = '') {
    $closeicon = new moodle_url('/local/moodle_survey/pix/close-icon.svg');
    $modal = html_writer::div(
        html_writer::div(
            html_writer::start_div('d-flex justify-content-between modal-content-section') .
                html_writer::tag('span', $modallabel , ['class' => 'modal-title']) .
                html_writer::div(
                    html_writer::link('#', html_writer::tag('img', '', ['src' => $closeicon, 'alt' => 'Icon', 'class' => 'close-icon']), ['class' => 'close', 'id' => 'close-modal']),
                    'modal-header'
                ) .
            html_writer::end_div() .
            $modaldescription,
            'modal-content'
        ),
        'modal'
    );
    echo add_modal_script();
    return $modal;
}


function add_modal_script() {
    return html_writer::script("
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.querySelector('.modal');
            var openModalButton = document.getElementById('open-modal');
            var closeModalButton = document.getElementById('close-modal');

            if (openModalButton && modal) {
                openModalButton.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default anchor behavior
                    modal.style.display = 'block';
                });
            }

            if (closeModalButton && modal) {
                closeModalButton.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }

            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    ");
}
