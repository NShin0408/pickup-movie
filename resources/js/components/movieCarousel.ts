export class MovieCarousel {
  private containers: HTMLElement[] = [];
  
  constructor() {
    this.init();
  }
  
  private init(): void {
    // カルーセルコンテナを取得
    this.containers = Array.from(document.querySelectorAll('.relative.overflow-hidden'));
    
    // 各カルーセルを初期化
    this.containers.forEach(container => {
      const items = container.querySelector('.flex.transition-transform');
      if (!items) return;
      
      const itemElements = Array.from(items.querySelectorAll('.flex-none'));
      const itemWidth = 200; // 180px + 左右パディング20px
      const visibleItems = Math.floor((container.clientWidth - 80) / itemWidth); // 80pxはボタン分の余白
      const totalItems = itemElements.length;
      
      // 初期状態でボタンの表示/非表示を設定
      const buttons = container.querySelectorAll('button');
      if (totalItems <= visibleItems) {
        buttons.forEach(button => button.style.display = 'none');
      }
      
      // カルーセルの現在位置
      container.setAttribute('data-position', '0');
      
      // ボタンのイベントリスナーを設定
      const nextBtn = container.querySelector('button:nth-child(3)');
      const prevBtn = container.querySelector('button:nth-child(2)');
      
      if (nextBtn) {
        nextBtn.addEventListener('click', () => this.moveNext(container));
      }
      
      if (prevBtn) {
        prevBtn.addEventListener('click', () => this.movePrev(container));
      }
    });
    
    // リサイズイベントリスナー
    window.addEventListener('resize', this.handleResize.bind(this));
  }
  
  private moveNext(container: HTMLElement): void {
    const items = container.querySelector('.flex.transition-transform') as HTMLElement;
    if (!items) return;
    
    const itemWidth = 200; // 180px + 左右パディング20px
    const itemElements = Array.from(items.querySelectorAll('.flex-none'));
    const totalItems = itemElements.length;
    const visibleItems = Math.floor((container.clientWidth - 80) / itemWidth); // 80pxはボタン分の余白
    
    let position = parseInt(container.getAttribute('data-position') || '0');
    
    // 次のポジションを計算（最大値を超えないようにする）
    position = Math.min(position + visibleItems, totalItems - visibleItems);
    container.setAttribute('data-position', position.toString());
    
    // カルーセルを移動
    items.style.transform = `translateX(-${position * itemWidth}px)`;
  }
  
  private movePrev(container: HTMLElement): void {
    const items = container.querySelector('.flex.transition-transform') as HTMLElement;
    if (!items) return;
    
    const itemWidth = 200; // 180px + 左右パディング20px
    const visibleItems = Math.floor((container.clientWidth - 80) / itemWidth); // 80pxはボタン分の余白
    
    let position = parseInt(container.getAttribute('data-position') || '0');
    
    // 前のポジションを計算（0未満にならないようにする）
    position = Math.max(position - visibleItems, 0);
    container.setAttribute('data-position', position.toString());
    
    // カルーセルを移動
    items.style.transform = `translateX(-${position * itemWidth}px)`;
  }
  
  private handleResize(): void {
    this.containers.forEach(container => {
      const items = container.querySelector('.flex.transition-transform') as HTMLElement;
      if (!items) return;
      
      const itemElements = Array.from(items.querySelectorAll('.flex-none'));
      const itemWidth = 200; // 180px + 左右パディング20px
      const visibleItems = Math.floor((container.clientWidth - 80) / itemWidth); // 80pxはボタン分の余白
      const totalItems = itemElements.length;
      
      let position = parseInt(container.getAttribute('data-position') || '0');
      
      // ポジションを再調整
      position = Math.min(position, totalItems - visibleItems);
      position = Math.max(position, 0);
      container.setAttribute('data-position', position.toString());
      
      // カルーセルを移動
      items.style.transform = `translateX(-${position * itemWidth}px)`;
      
      // ボタンの表示/非表示を設定
      const buttons = container.querySelectorAll('button');
      buttons.forEach(button => {
        button.style.display = totalItems <= visibleItems ? 'none' : '';
      });
    });
  }
}