<?php
namespace Amplify;

use Simplify\Renderable;

class DashboardModule extends Renderable
{

    public function render()
    {
        return $this->getView();
    }
}