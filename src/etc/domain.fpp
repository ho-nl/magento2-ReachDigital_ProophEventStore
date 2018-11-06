namespace ReachDigital\ProophEventStore\Infrastructure\Pdo {
    data DbType = MariaDb | MySql deriving(Enum);
}

namespace ReachDigital\ProophEventStore\Infrastructure\Projection {
    data TableName = String deriving(FromString, ToString);
    data PrimaryKey = String deriving(FromString, ToString);
}
