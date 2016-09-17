<?php

namespace Tagd\Views;

/**
 * NB: this differs from other views in that it does not extend Base and does not
 * render itself.  Instead if it consumed by the template file itself as it
 * renders.  This is because this viewer backs the WordPress-style page template,
 * not the Tagd-style templates that are included by Tagd-style views.
 */
class ViewerView {
    
}