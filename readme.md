# requires

Image_Graphviz : PEAR

# usage

## EightPz（盤面）を作成

### デフォルト

```
$eightPz = new EightPz([]);
```

### 盤面を指定

```
$eightPz = new EightPz([0,3,2,6,1,5,7,4,8]);
```

で、

```
0 3 2
6 1 5
7 4 8
```

の盤面が作成される

### 盤面をランダムに動かす

```
$eightPz->shuffle(10);
```

引数は動かす回数

## Solverを作成

```
$solver = new Solver($eightPz,"manhattan",false);
```

引数
- $eightPzに初期盤面
- "manhattan" or "howmanyWrong" はコスト関数
- IDA*にする（true）か、A*にする（false）か

## Solverを実行

```
$solver->run(false);
```

引数は、実行中の仮定を出力するか（デフォルト：true）

## Solverを評価

```
$solver->evaluate();
```

memory：OPENに格納した最大ノード数
time：計算時間（計算時間のリミットはSolverクラス内にハードコーディングされている）


## 画像を出力
```
$solver->makeTree("filename-tree");
$solver->Trail("filename-trail");
```

makeTree() は解析木
trail() はパズルの回答を
SVGで出力
