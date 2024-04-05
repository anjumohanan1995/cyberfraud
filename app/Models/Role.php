<?php  
  
namespace App\Models;  
  
use Jenssegers\Mongodb\Eloquent\Model as Eloquent; 
use Illuminate\Database\Eloquent\SoftDeletes; 

  
class Role extends Eloquent  
{  
    use SoftDeletes; 

    protected $connection = 'mongodb';
      
    protected $collection = 'roles';  
  
    /**  
     * The attributes which are mass assigned will be used.  
     *  
     * It will return @var array  
     */  
    protected $fillable = [  
        'name','user_id'
    ];  

    public function user() {
        return $this->belongsTo('App\User');
    }
}  