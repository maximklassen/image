<?php
declare(strict_types=1);
namespace MaximKlassen\Image\Pipeline;
use MaximKlassen\Image\Image;
use MaximKlassen\Image\Contracts\DriverInterface;
use MaximKlassen\Image\Pipeline\Operations\OperationInterface;
use MaximKlassen\Image\Pipeline\Operations\AutoOrient;
use MaximKlassen\Image\Pipeline\Operations\Crop;
use MaximKlassen\Image\Pipeline\Operations\Fit;
use MaximKlassen\Image\Pipeline\Operations\Resize;
use MaximKlassen\Image\Pipeline\Operations\Convert;
use MaximKlassen\Image\Pipeline\Operations\StripMetadata;
use MaximKlassen\Image\Value\Format;
final class Pipeline {
    /** @var OperationInterface[] */ private array $ops = [];
    private function __construct(private Image $image) {}
    public static function from(Image $img): self { return new self($img); }
    public static function of(Image $img, array $ops): self { $p=new self($img); foreach($ops as $op) $p->ops[]=$op; return $p; }
    public function autoOrient(): self { $this->ops[] = new AutoOrient(); return $this; }
    public function crop(int $x,int $y,int $w,int $h): self { $this->ops[] = new Crop($x,$y,$w,$h); return $this; }
    /** @param array{0:string,1:string}|array{string,string} $position */
    public function fit(int $width,int $height,string $mode='cover', array $position=['center','center']): self {
        $this->ops[] = new Fit($width,$height,$mode,$position[0] ?? 'center',$position[1] ?? 'center'); return $this;
    }
    public function resize(int $width,int $height): self { $this->ops[] = new Resize($width,$height); return $this; }
    public function convert(string $format): self {
        $mime = str_contains($format,'/') ? $format : match(strtolower($format)){ 'jpg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp', default=>throw new \InvalidArgumentException('Unknown format: '.$format) };
        $ext = match($mime){ 'image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp', default=>'bin'};
        $this->ops[] = new Convert(new Format($mime,$ext)); return $this;
    }
    public function stripMetadata(): self { $this->ops[] = new StripMetadata(); return $this; }
    /** @return array<int,array<string,mixed>> */
    public function operationsArray(): array { $arr=[]; foreach($this->ops as $op){ $arr[]=$op->toArray(); } return $arr; }
    public function getImage(): Image { return $this->image; }
    public function apply(DriverInterface $driver): Image { $img=$this->image; foreach($this->ops as $op){ $img=$driver->apply($img,$op);} return $img; }
}
