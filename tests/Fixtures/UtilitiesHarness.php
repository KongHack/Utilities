<?php

namespace GCWorld\Utilities\Tests\Fixtures;

use GCWorld\Utilities\Traits\CLI;
use GCWorld\Utilities\Traits\Colors;
use GCWorld\Utilities\Traits\Curl;
use GCWorld\Utilities\Traits\FancyArrayTrait;
use GCWorld\Utilities\Traits\General;
use GCWorld\Utilities\Traits\Image;
use GCWorld\Utilities\Traits\JSONTrait;
use GCWorld\Utilities\Traits\Str;
use GCWorld\Utilities\Traits\Time;

final class UtilitiesHarness
{
    use CLI;
    use Colors;
    use Curl;
    use FancyArrayTrait;
    use General;
    use Image;
    use JSONTrait;
    use Str;
    use Time;
}
