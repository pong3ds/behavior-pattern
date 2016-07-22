## Behavior Pattern เพื่อการศึกษา Composition over Inheritance

จากกรณีศึกษา Bird กับ IronMan

1. Bird บินได้ และ ร้องจิ๊บๆได้
1. IronMan ไม่ใช่ Bird แต่ IronMan บินได้
1. IronMan ร้องจิ๊บๆไม่ได้
1. IronMan ยิง Missile ได้
1. Bird ยิง Missile ไม่ได้

สังเกตุว่า ถ้าใช้ Inheritance model ในกรณีนี้ จะไม่ make sense เนื่องจาก

1. ถ้าให้ class Bird บินได้ และ ร้องเพลงได้ แล้ว IronMan inherit จาก Bird จะทำให้ IronMan ร้องเพลงได้ด้วยซึ่งไม่ cool
1. ให้ IronMan สืบทอดจาก Bird แสดงว่า IronMan เป็น Bird เหรอ? ไม่น่าจะใช่
1. ถ้าให้ IronMan บินได้ ยิง Missile ได้ แล้วให้ Bird Inherit จาก IronMan จะทำให้ Bird ยิง Missile ได้ซึ่งไม่ใช่
1. ถ้าให้ Bird สืบทอดจาก IronMan แสดงว่า Bird เป็น IronMan ซึ่งผิด

ให้ดูใน Directory example จะเห็นว่า ถ้าใช้ Composition แล้วจะได้ class Bird หน้าตาแบบนี้:

    class Bird extends Extendable{

        public $implement = [
            Flyable::class,
            JibJibable::class
        ];

        public function __construct(){
            parent::__construct();
        }
    }

คลาส IronMan จะหน้าตาแบบนี้:

    class IronMan extends Extendable{

        public $implement = [
            Flyable::class,
            Fireable::class
        ];

        public function __construct(){
            parent::__construct();
        }
    }

Code Test เป็นแบบนี้:

    function run(){
        $bird = new Bird();
        $ironMan = new IronMan();

        tryToFly($bird);
        tryToJibJib($bird);
        tryToFire($bird);

        tryToFly($ironMan);
        tryToJibJib($ironMan);
        tryToFire($ironMan);

    }

    function tryToFly($obj){
        if($obj->hasAbilityTo(Flyable::class)){
            $obj->fly();
        }
    }

    function tryToFire($obj){
        if($obj->hasAbilityTo(Fireable::class)){
            $obj->fire();
        }
    }

    function tryToJibJib($obj){
        if($obj->hasAbilityTo(JibJibable::class)){
            $obj->jibjib();
        }
    }

ลอง php Test.php จะได้ผลลัพท์แบบนี้:

	I am a Bird , and I am flying
    I am a Bird , and I am Jib Jib
    I am a IronMan , and I am flying
    I am a IronMan , and I am firing missile

Credit Extendable.php และ ExtendableTrait.php จาก OctoberCMS http://octobercms.com