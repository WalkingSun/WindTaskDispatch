<?php
namespace App\Models\Entity;

use Swoft\Db\Model;
use Swoft\Db\Bean\Annotation\Column;
use Swoft\Db\Bean\Annotation\Entity;
use Swoft\Db\Bean\Annotation\Id;
use Swoft\Db\Bean\Annotation\Required;
use Swoft\Db\Bean\Annotation\Table;
use Swoft\Db\Types;

/**
 * @Entity()
 * @Table(name="jp_task")
 * @uses      JpTask
 */
class JpTask extends Model
{
    /**
     * @var string $content 
     * @Column(name="content", type="string", length=255, default="")
     */
    private $content;

    /**
     * @var string $createtime 
     * @Column(name="createtime", type="datetime")
     */
    private $createtime;

    /**
     * @var string $cron 
     * @Column(name="cron", type="string", length=255)
     * @Required()
     */
    private $cron;

    /**
     * @var int $id 
     * @Id()
     * @Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var int $isdelete 
     * @Column(name="isdelete", type="integer")
     */
    private $isdelete;

    /**
     * @var string $title 
     * @Column(name="title", type="string", length=64)
     * @Required()
     */
    private $title;

    /**
     * @param string $value
     * @return $this
     */
    public function setContent(string $value): self
    {
        $this->content = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatetime(string $value): self
    {
        $this->createtime = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCron(string $value): self
    {
        $this->cron = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setId(int $value)
    {
        $this->id = $value;

        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setIsdelete(int $value): self
    {
        $this->isdelete = $value;

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTitle(string $value): self
    {
        $this->title = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getCreatetime()
    {
        return $this->createtime;
    }

    /**
     * @return string
     */
    public function getCron()
    {
        return $this->cron;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getIsdelete()
    {
        return $this->isdelete;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function add( $data=[] ){

        if( !$data ) throw new \Exception('data must be Array');

        foreach ($data as $k=>$v){
            //todo 对cron命令进行检测
            if( $k=='cron' ){

            }

            $setField = 'set'.ucfirst( $k );
            $this->$setField($v);
        }

        if( !empty($data['id']) ){
            if( !$this::findById($data['id'])->getResult() ) throw new \Exception('记录不存在');
            $this->update();
            $taskId = $data['id'];
        }else{

            $taskId = $this->save()->getResult();
        }

        return $taskId;
    }
}
