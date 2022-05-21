<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MainCategoryRequest;
use Illuminate\Support\Facades\Config;
use App\Models\Main_Category;
use DB;
use Illuminate\Support\Str;

class MainCategriesController extends Controller
{
    public function index(){
      $default_lang = get_default_lang();
      $categories = Main_Category::where('translation_lang', $default_lang)->selection() ->get();
       return view('admin.maincategories.index' , compact('categories'));
    }

    public function create(){
      return view('admin.maincategories.create');
    }

    public function store(MainCategoryRequest $request){

    try {
          //return $request;
      $main_categories = collect($request->category); // انا هنا جبت كل الكاتيحورى و حولته الى كوليكشن علشان اقدر اعمل فلتر عليه

        $filter = $main_categories->filter(function ($value, $key) { // كود الفلتر وظهور Default Languages
          return $value['abbr'] == get_default_lang();
      });

      $default_category = array_values($filter->all()) [0];


      $filePath = "";
      if ($request->has('photo')) {

          $filePath = uploadImage('maincategories', $request->photo);
      }

       DB::beginTransaction();

       $default_category_id = Main_Category::insertGetId([
          'translation_lang' => $default_category['abbr'],
          'translation_of' => 0,
          'name' => $default_category['name'],
          'slug' => $default_category['name'],
          'photo' => $filePath
      ]);


      $categories = $main_categories->filter(function ($value, $key) { // كود الفلتر وظهور Default Languages
          return $value['abbr'] != get_default_lang();

      });

      if(isset($categories) && $categories ->count()){
        $categories_arr = [];
        foreach ($categories as $category) {
          $categories_arr[] = [
            'translation_lang' => $category['abbr'],
            'translation_of' => $default_category_id,
            'name' => $category['name'],
            'slug' => $category['name'],
            'photo' => $filePath
          ];
        }
        Main_Category::insert($categories_arr);
      }
      DB::commit();
        return redirect()->route('admin.categories')->with(['success' => 'تم الحفظ بنجاح']);

        } catch (\Exception $ex) {
            DB::rollback(); // ما تنفيذش اى حاجه علشان كلهم معتمدين على بعض
            return redirect()->route('admin.categories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }


    public function edit($mainCat_id){ // تعديل اقسام المتاجر بناءا على اللغه الافتراضية
    $mainCategory = Main_Category::with('categories')->selection()->find($mainCat_id);
      if(!$mainCategory){
        return redirect()->route('admin.categories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
      }

      return view('admin.maincategories.edit' , compact('mainCategory'));

    }

    public function update($mainCat_id , MainCategoryRequest $request){
    try{
      $main_category = Main_Category::find($mainCat_id);
      if(!$main_category){
        return redirect()->route('admin.categories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
      }

      // update date

       $category = array_values($request->category) [0];
       if(!$request ->has('category.0.active')){
         $request->request->add(['active' => 0]);
       }else{
         $request->request->add(['active' => 1]);
       }


       Main_Category::where('id' , $mainCat_id)->update([
        'name' => $category['name'],
        'active' => $request->active,

      ]);

      // Save photo
      $filePath = "";
      if ($request->has('photo')) {
          $filePath = uploadImage('maincategories', $request->photo);

          Main_Category::where('id' , $mainCat_id)->update([
           'photo' => $filePath
         ]);
      }

        return redirect()->route('admin.categories')->with(['success' => 'تم التحديث بنجاح']);

    }catch(\Exception $ex){
      return redirect()->route('admin.categories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
    }

  }

  public function delete($mainCat_id){

    try{
      $maincategory = Main_Category::find($mainCat_id);

      if(!$maincategory){
          return redirect()->route('admin.categories')->with(['error' => 'هذا القسم غير موجود ']);
      }

      $vendors = $maincategory->vendors();
      if (isset($vendors) && $vendors->count() > 0) {
          return redirect()->route('admin.categories')->with(['error' => 'لأ يمكن حذف هذا القسم  ']);
      }

      $maincategory -> delete();
      // update date

        return redirect()->route('admin.categories')->with(['success' => 'تم حذف القسم بنجاح']);

    }catch(\Exception $ex){
      return redirect()->route('admin.categories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
    }

  }

  public function changeStatus($id){

    try {
      $maincategory = Main_Category::find($id);
      if (!$maincategory)
                  return redirect()->route('admin.categories')->with(['error' => 'هذا القسم غير موجود ']);

        $status = $maincategory -> active == 0 ? 1 : 0;

       $maincategory -> update(['active' =>$status ]);
       return redirect()->route('admin.categories')->with(['success' => ' تم تغيير الحالة بنجاح ']);
    } catch (\Exception $e) {
      return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);

    }


  }






}
