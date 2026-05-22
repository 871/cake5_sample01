# Coding Rules
- PHP 8.3 を使用
- CakePHP 5 前提
- DDD + Clean Architecture
- Repository は interface 経由で利用
- ValueObject を優先
- ORM Entity を Domain に渡さない
- 日本語コメントを使用

---
# Controller のルール
- コントローラはユースケースごとに作成
- 処理対象はnamespaceで[カテゴリ(Category)]と表現、処理内容はクラス名で表現する 
```
App\Controller\Admin\AdminAccount\CreateController
App\Controller\Admin\AdminAccount\SearchController
App\Controller\Admin\AdminAccount\DetailController
App\Controller\Admin\AdminAccount\EditController
App\Controller\Admin\AdminAccount\DeleteController

# Service クラスのルール
- ドメイン駆動設計のアプリケーション層に当たる処理を記述する
- コントローラごとに作成する（CtlService）
- 処理対象のカテゴリで共通的に扱いたい処理がある場合は、カテゴリ名でServiceクラスを作成（CategoryService）してCtlService経由で呼び出す
```
App\Service\Admin\AdminAccount\CreateService
App\Service\Admin\AdminAccount\SearchService
App\Service\Admin\AdminAccount\DetailService
App\Service\Admin\AdminAccount\EditService
App\Service\Admin\AdminAccount\DeleteService
App\Service\Admin\AdminAccountService

