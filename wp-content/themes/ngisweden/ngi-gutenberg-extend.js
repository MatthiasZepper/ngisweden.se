
////// ngi-gutenberg-extend.js
// This file contains javascript code to customise the behaviour
// of the WordPress Gutenberg block editor.

// Always add the 'table' class to new tables (front end)
function ngiAddBlockClassNames( props, blockType ) {
    if(blockType.name == 'core/table') {
        props.children.props.className = 'table table-striped table-bordered';
        return props;
    }
    return props;
}
wp.hooks.addFilter(
    'blocks.getSaveContent.extraProps',
    'ngi-guten-extend/add-block-frontend-class-names',
    ngiAddBlockClassNames
);
