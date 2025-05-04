export class MovieCarousel {
  private containers: HTMLElement[] = [];

  constructor() {
    this.init();
  }

  private init(): void {
    // DOMが完全に読み込まれてからカルーセルを初期化
    setTimeout(() => {
      // カルーセルコンテナを取得
      this.containers = Array.from(document.querySelectorAll('[id$="-carousel"]'));

      // 各カルーセルを初期化
      this.containers.forEach(container => {
        const items = container.querySelector('.flex.transition-transform');
        if (!items) return;

        const itemElements = Array.from(items.querySelectorAll('.flex-none'));
        if (itemElements.length === 0) return;

        // 実際の要素の幅を測定
        const firstItem = itemElements[0] as HTMLElement;
        const itemRect = firstItem.getBoundingClientRect();
        const itemWidth = itemRect.width; // 実際の表示幅を取得

        const visibleItems = Math.floor((container.clientWidth - 60) / itemWidth); // ボタン分の余白調整
        const totalItems = itemElements.length;

        // カルーセルの現在位置
        container.setAttribute('data-position', '0');
        container.setAttribute('data-item-width', itemWidth.toString());

        // 初期状態でボタンの表示/非表示を設定
        const nextBtn = container.querySelector('button:nth-child(3)') as HTMLElement;
        const prevBtn = container.querySelector('button:nth-child(2)') as HTMLElement;

        if (totalItems <= visibleItems) {
          // アイテム数が表示可能数以下の場合は両方のボタンを非表示
          if (nextBtn) nextBtn.style.display = 'none';
          if (prevBtn) prevBtn.style.display = 'none';
        } else {
          // 左ボタンは初期状態では非表示（ポジション0のため）
          if (prevBtn) prevBtn.style.display = 'none';
          // 右ボタンは表示
          if (nextBtn) nextBtn.style.display = '';
        }

        // ボタンのイベントリスナーを設定
        if (nextBtn) {
          nextBtn.addEventListener('click', () => this.moveNext(container));
        }

        if (prevBtn) {
          prevBtn.addEventListener('click', () => this.movePrev(container));
        }
      });

      // リサイズイベントリスナー
      window.addEventListener('resize', this.handleResize.bind(this));
    }, 100); // わずかな遅延を入れてDOMの準備を確実にする
  }

  private moveNext(container: HTMLElement): void {
    const items = container.querySelector('.flex.transition-transform') as HTMLElement;
    if (!items) return;

    const itemWidth = parseFloat(container.getAttribute('data-item-width') || '200');
    const itemElements = Array.from(items.querySelectorAll('.flex-none'));
    const totalItems = itemElements.length;
    const visibleItems = Math.floor((container.clientWidth - 60) / itemWidth);

    let position = parseInt(container.getAttribute('data-position') || '0');

    // 次のポジションを計算（最大値を超えないようにする）
    position = Math.min(position + visibleItems, totalItems - visibleItems);
    container.setAttribute('data-position', position.toString());

    // カルーセルを移動
    items.style.transform = `translateX(-${position * itemWidth}px)`;

    // ボタンの表示/非表示を更新
    this.updateButtonVisibility(container, position, totalItems, visibleItems);
  }

  private movePrev(container: HTMLElement): void {
    const items = container.querySelector('.flex.transition-transform') as HTMLElement;
    if (!items) return;

    const itemWidth = parseFloat(container.getAttribute('data-item-width') || '200');
    const itemElements = Array.from(items.querySelectorAll('.flex-none'));
    const totalItems = itemElements.length;
    const visibleItems = Math.floor((container.clientWidth - 60) / itemWidth);

    let position = parseInt(container.getAttribute('data-position') || '0');

    // 前のポジションを計算（0未満にならないようにする）
    position = Math.max(position - visibleItems, 0);
    container.setAttribute('data-position', position.toString());

    // カルーセルを移動
    items.style.transform = `translateX(-${position * itemWidth}px)`;

    // ボタンの表示/非表示を更新
    this.updateButtonVisibility(container, position, totalItems, visibleItems);
  }

  private updateButtonVisibility(container: HTMLElement, position: number, totalItems: number, visibleItems: number): void {
    const prevBtn = container.querySelector('button:nth-child(2)') as HTMLElement;
    const nextBtn = container.querySelector('button:nth-child(3)') as HTMLElement;

    if (prevBtn) {
      // 左ボタンはポジションが0より大きい場合のみ表示
      prevBtn.style.display = position > 0 ? '' : 'none';
    }

    if (nextBtn) {
      // 右ボタンは右に移動できる余地がある場合のみ表示
      nextBtn.style.display = position < totalItems - visibleItems ? '' : 'none';
    }
  }

  private handleResize(): void {
    this.containers.forEach(container => {
      const items = container.querySelector('.flex.transition-transform') as HTMLElement;
      if (!items) return;

      const itemElements = Array.from(items.querySelectorAll('.flex-none'));
      if (itemElements.length === 0) return;

      // 実際の要素の幅を再測定
      const firstItem = itemElements[0] as HTMLElement;
      const itemRect = firstItem.getBoundingClientRect();
      const itemWidth = itemRect.width;
      container.setAttribute('data-item-width', itemWidth.toString());

      const visibleItems = Math.floor((container.clientWidth - 60) / itemWidth);
      const totalItems = itemElements.length;

      let position = parseInt(container.getAttribute('data-position') || '0');

      // ポジションを再調整
      position = Math.min(position, totalItems - visibleItems);
      position = Math.max(position, 0);
      container.setAttribute('data-position', position.toString());

      // カルーセルを移動
      items.style.transform = `translateX(-${position * itemWidth}px)`;

      // ボタンの表示/非表示を更新
      if (totalItems <= visibleItems) {
        // アイテム数が表示可能数以下の場合は両方のボタンを非表示
        const buttons = container.querySelectorAll('button');
        buttons.forEach(button => {
          button.style.display = 'none';
        });
      } else {
        this.updateButtonVisibility(container, position, totalItems, visibleItems);
      }
    });
  }
}
